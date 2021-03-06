<?php

namespace Mink67\KafkaConnect\Services;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Symfony\Routing\IriConverter;
use Doctrine\ORM\EntityManagerInterface;
use Mink67\KafkaConnect\Annotations\Readers\ReaderConfig;
use Mink67\KafkaConnect\Contracts\Crypt;
use Mink67\KafkaConnect\Db\Db;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;

use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Enqueue\RdKafka\RdKafkaMessage;
use Enqueue\RdKafka\RdKafkaContext;
use Enqueue\RdKafka\RdKafkaConsumer;
use Mink67\KafkaConnect\Db\LockingDb;

/**
 * Perment de créer un un kafka connect
 */
class Receive {

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var ReaderConfig
     */
    private $reader;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var 
     */
    private $context;

    /**
     * 
     */
    public function __construct(
        EntityManagerInterface $em, 
        ReaderConfig $reader, 
        ContainerInterface $container,
        LockingDb $lockingDb,
        IriConverterInterface $iriConverter
    ) {
        $this->em = $em;
        $this->reader = $reader;
        $this->container = $container;
        $this->lockingDb = $lockingDb;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @return RdKafkaContext
     */
    public function getContext()
    {
        if (is_null($this->context)) {

            $host = $this->container->getParameter("mink67.kafka_connect.consumer.bootstrap_servers");

            $connectionFactory = new RdKafkaConnectionFactory([
                'global' => [
                    'group.id' => uniqid('', true),
                    'metadata.broker.list' => $host,
                    'enable.auto.commit' => 'false',
                ],
                'topic' => [
                    'auto.offset.reset' => 'beginning',
                ],
            ]);
    
            $context = $connectionFactory->createContext();
    
            $this->context = $context;
        }

        return $this->context;

    }
    /**
     * @return RdKafkaConsumer
     */
    public function getConcumer()
    {
        $context = $this->getContext();

        $prefix = $this->container->getParameter("mink67.kafka_connect.prefix_channel");
        $topicName = $prefix ."_" . $this->container->getParameter("mink67.kafka_connect.default_topic_name");

        //dump($topicName);

        $fooQueue = $context->createTopic($topicName);

        $consumer = $context->createConsumer($fooQueue);

        return $consumer;
    }

    /**
     * 
     */
    public function __invoke(array $data = [])
    {
        
        $consumer = $this->getConcumer();


        $message = $consumer->receive();
        
        
        $consumer->acknowledge($message);

        return $message->getBody();
    }

}