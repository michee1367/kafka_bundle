<?php

namespace Mink67\KafkaConnect\Db;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Statement;
use Mink67\KafkaConnect\Contracts\Param as ContractsParam;
use  Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

/**
 * Perment de créer un un kafka connect
 */
class Param implements ContractsParam {
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $value;
    /**
     * 
     */
    public function __construct(string $name, string $value) {
        $this->name = $name;
        $this->value = $value;
    }
    
    /**
     * @return string
     * @throws Exception
     * 
     */
    public function getName(): string {
        return $this->name;
    }
    
    /**
     * @return string
     * @throws Exception
     */
    public function getValue(): string {
        return $this->value;
    }
    
}