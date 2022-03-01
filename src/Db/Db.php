<?php

namespace Mink67\KafkaConnect\Db;

use Mink67\KafkaConnect\Contracts\ConcreteDb;
use Mink67\KafkaConnect\Contracts\Crypt;
use Mink67\KafkaConnect\Contracts\Param;

/**
 * Perment de créer un un kafka connect
 */
class Db {
    /**
     * @var ConcreteDb
     */
    private $db;
    /**
     * @var Crypt
     */
    private $crypt;

    /**
     * 
     */
    public function __construct(ConcreteDb $db, Crypt $crypt) {
        $this->db = $db;
        $this->crypt = $crypt;
    }

    /**
     * @return Param
     * @param string $key
     * @throws Exception
     * 
     */
    public function getParam(string $key): ?Param
    {
        $param = $this->db->getParam($key);

        return $param;
    }
    /**
     * @return self
     * @param string $key
     * @param string $value
     * @throws Exception
     */
    public function setParam(string $key, string $value): self
    {
        $cipherText = $this->crypt->encrypt($value);

        $this->db->setParam($key, $cipherText);

        return $this;
        
    }
    /**
     * 
     */
    public function getClearValue(string $key): ?string
    {
        $param = $this->getParam($key);

        if (is_null($param)) {
            return null;
        }

        return $this->crypt->decrypt($param->getValue());
    }
}