<?php

namespace Mink67\KafkaConnect\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Mink67\KafkaConnect\Annotations\Readers\ReaderConfig;

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
     * 
     */
    public function __construct(ReaderConfig $reader, ContainerInterface $container) {
        $this->reader = $reader;
        $this->container = $container;
    }

    /**
     * 
     */
    public function __invoke($entity)
    {
        $class = get_class($entity);
        $classConcern = $this->reader->getConfigCopyable($class);

        

        dd($classConcern);

    }
}