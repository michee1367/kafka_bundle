<?php

namespace Mink67\KafkaConnect\Services;

use Doctrine\ORM\EntityManagerInterface;
use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mink67\KafkaConnect\Annotations\Readers\ReaderConfig;
use Mink67\KafkaConnect\Constant;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Mink67\KafkaConnect\Services\Utils\MessageDbValidator;

/**
 * Perment de créer un un kafka connect
 */
class Init {
    /**
     * @var Emit
     */
    private $emit;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var ReaderConfig
     */
    private $reader;
    /**
     * 
     */
    public function __construct(Emit $emit, EntityManagerInterface $em, ReaderConfig $reader) {
        $this->emit = $emit;
        $this->em = $em;
        $this->reader = $reader;
    }
    /**
     * 
     */
    public function __invoke()
    {
        $classes = $this->em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        foreach ($classes as $key => $className) {
        
            $config = $this->reader->getConfigCopyable($className);
    
            if (is_null($config)) {
                continue;
            }

            $entities = $this->em->getRepository($className)->findAll();
            $emit = $this->emit;

            foreach ($entities as $key => $entity) {
                $emit($entity, Constant::UPDATE_ACTION);
            }
            
        }
    }
}