<?php

namespace Mink67\KafkaConnect\Services;
use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mink67\KafkaConnect\Annotations\Readers\ReaderConfig;
use Mink67\KafkaConnect\Constant;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Mink67\KafkaConnect\Services\Utils\MessageDbValidator;

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
     * 
     */


    /**
     * 
     */
    public function __invoke($entity, int $action=null)
    {
        //define('RD_KAFKA_VERSION', 16908799);

        if (is_null($action)) {
            $action = Constant::CREATE_ACTION;
        }
        
        $class = get_class($entity);
        $config = $this->reader->getConfigCopyable($class);
        //dd($class);
        if (is_null($config)) {
            return;
        }

        //dd($config);
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
            return null;
        }

        $messageStr = json_encode($messageArr);
        $messageBase64 = base64_encode($messageStr);

        //dd($messageBase64);

        $groupId = uniqid('', true);
        $host = $this->container->getParameter("mink67.kafka_connect.producer.bootstrap_servers");
        $autoOffsetReset = "beginning";
        $topicName = $config->getTopicName();
        
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
        
        $message = $context->createMessage($messageBase64);
        
        $topic = $context->createTopic($topicName);
        
        $context->createProducer()->send($topic, $message);

    }
}