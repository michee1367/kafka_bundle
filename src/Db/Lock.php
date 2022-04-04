<?php

namespace Mink67\KafkaConnect\Db;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Statement;
use Mink67\KafkaConnect\Contracts\Param as ContractsParam;
use  Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

/**
 * Perment de créer un un kafka connect
 */
class Lock {
    /**
     * @var string
     */
    private $iri;
    /**
     * 
     */
    public function __construct(string $iri) {
        $this->iri = $iri;
    }
    
    /**
     * @return string
     * @throws Exception
     * 
     */
    public function getIri(): string {
        return $this->iri;
    }
}