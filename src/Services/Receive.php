<?php

namespace Mink67\KafkaConnect\Services;

use Doctrine\ORM\EntityManagerInterface;
use Mink67\KafkaConnect\Annotations\Readers\ReaderConfig;
use Mink67\KafkaConnect\Contracts\Crypt;
use Mink67\KafkaConnect\Db\Db;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;

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
     * 
     */
    public function __construct(EntityManagerInterface $em, ReaderConfig $reader, ContainerInterface $container) {
        $this->em = $em;
        $this->reader = $reader;
        $this->container = $container;
    }

    /**
     * 
     */
    public function __invoke(array $data = [])
    {


    }

}