<?php

namespace Mink67\KafkaConnect\Services\Cert;

use Mink67\KafkaConnect\Services\ManagerKeys;

/**
 * Perment de créer un un kafka connect
 */
class GeneratePayloadCert {

    /**
     * @var string
     */
    private $instType;
    /**
     * @var ManagerKeys
     */
    private $managerKeys;

    /**
     * 
     */
    public function __construct(string $instType, ManagerKeys $managerKeys) {
        $this->instType = $instType;
        $this->managerKeys = $managerKeys;
    }

    /**
     * 
     */
    public function __invoke(): array
    {
        //$channal_name = $this->instType .".". md5(random_bytes(10));
        $channal_name = $this->instType;
        $publicKey = $this->managerKeys->getPublicKey();

        $payload = array(
            "iss" => "http://rna.com/api/user",
            "aud" => "http://rna.com/api",
            "iat" => time(),
            "nbf" => 1357000000,
            "channal_name" => $channal_name,
            "type_inst" => $this->instType,
            "pkey" => $publicKey,
            "uuid" => "",
        );

        return $payload;        
    }
    /**
     * 
     */
    public function getChannalName()
    {
        
    }
}