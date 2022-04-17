<?php

namespace Mink67\KafkaConnect\Services;
use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mink67\KafkaConnect\Annotations\Readers\ReaderConfig;
use Mink67\KafkaConnect\Constant;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Doctrine\Common\Util\ClassUtils;
use Mink67\KafkaConnect\Services\Utils\MessageDbValidator;
use Enqueue\RdKafka\RdKafkaContext;
use Enqueue\RdKafka\RdKafkaProducer;


/**
 * Perment de créer un un kafka connect
 */
class Emit {

    /**
     * @var ReaderConfig
     */
    private $reader;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var NormalizerInterface
     */
    private $normalizer;
    /**
     * @var MessageDbValidator
     */
    private $validator;
    /**
     * @var RdKafkaContext
     */
    private $context;
    /**
     * @var RdKafkaProducer
     */
    private $productor;

    /**
     * 
     */
    public function __construct(
        ReaderConfig $reader,
        ContainerInterface $container, 
        NormalizerInterface $normalizer,
        MessageDbValidator $validator
    ) {
        $this->reader = $reader;
        $this->container = $container;
        $this->normalizer = $normalizer;
        $this->validator = $validator;
    }
    /**
     * @return RdKafkaContext
     */
    public function getContext()
    {
        if (is_null($this->context)) {
            

            $groupId = uniqid('', true);
            $host = $this->container->getParameter("mink67.kafka_connect.producer.bootstrap_servers");
            $autoOffsetReset = "beginning";

            
            $connectionFactory = new RdKafkaConnectionFactory([
                'global' => [
                    'group.id' => $groupId,
                    'metadata.broker.list' => $host,
                    'enable.auto.commit' => 'false',
                ],
                'topic' => [
                    'auto.offset.reset' => $autoOffsetReset,
                ],
            ]);
            
            $context = $connectionFactory->createContext();
            $this->context = $context;
        }

        return $this->context;
        
    }
    /**
     * @return RdKafkaProducer
     */
    public function getProducer()
    {
        if (is_null($this->productor)) {

            $context = $this->getContext();
    
            $productor = $context->createProducer();

            $this->productor = $productor;
            
        }

        return $this->productor;
    }


    /**
     * 
     */
    public function __invoke($entity, int $action=null)
    {
        //define('RD_KAFKA_VERSION', 16908799);

        if (is_null($action)) {
            $action = Constant::CREATE_ACTION;
        }
        
        $class = ClassUtils::getClass($entity);
        $config = $this->reader->getConfigCopyable($class);
        //dd($class);
        if (is_null($config)) {
            dump($class);
            dump("config invalid");
            return;
        }

        //dump($config);
        $groups = $config->getGroups();

        $dataArr = $this->normalizer
                        ->normalize(
                            $entity, 
                            null,
                            [
                                'groups' => $groups,
                            ]
                    );
        
        $messageArr = [
                    'data' => $dataArr,
                    'action' => $action,
                    'metaData' => [
                        'groups' => $groups,
                        //'groups' => [],
                        'resourceName' => $config->getResourceName(),
                    ],
                ];//::


        $resultValid = $this->validator->validate($messageArr);
        //dd($resultValid);
        if (!$resultValid) {
            dump($class);
            dump("config invalid");
            return null;
        }

        $messageStr = json_encode($messageArr);
        $messageBase64 = base64_encode($messageStr);

        //dd($messageBase64);

        /*$groupId = uniqid('', true);
        $host = $this->container->getParameter("mink67.kafka_connect.producer.bootstrap_servers");
        $autoOffsetReset = "beginning";

        
        $connectionFactory = new RdKafkaConnectionFactory([
            'global' => [
                'group.id' => $groupId,
                'metadata.broker.list' => $host,
                'enable.auto.commit' => 'false',
            ],
            'topic' => [
                'auto.offset.reset' => $autoOffsetReset,
            ],
        ]);*/

        $prefix = $this->container->getParameter("mink67.kafka_connect.prefix_channel");
        $topicName = $prefix ."_". $config->getTopicName();
        
        //$context = $connectionFactory->createContext();
        $context = $this->getContext();
        
        $message = $context->createMessage($messageBase64);
        
        $topic = $context->createTopic($topicName);
        
        $this->getProducer()->send($topic, $message);

        //dd($topic);

    }
}