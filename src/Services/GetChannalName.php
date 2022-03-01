<?php

namespace Mink67\KafkaConnect\Services\Cert;

use Mink67\KafkaConnect\Contracts\Crypt;
use Mink67\KafkaConnect\Db\Db;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Perment de créer un un kafka connect
 */
class GetChannalName {

    /**
     * @var string
     */
    private $instType;
    /**
     * @var Db
     */
    private $db;

    /**
     * 
     */
    public function __construct(string $instType, Db $db) {
        $this->instType = $instType;
        $this->db = $db;
    }

    /**
     * 
     */
    public function __invoke(): string
    {
        $channal_name = $this->instType .".". md5(random_bytes(10));
        

        return $channal_name;        
    }
    /**
     * 
     */
    public function getChannalName()
    {
        
    }
}