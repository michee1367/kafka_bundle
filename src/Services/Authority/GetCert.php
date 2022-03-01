<?php

namespace Mink67\KafkaConnect\Services\Authority;

use Mink67\KafkaConnect\Contracts\Crypt;
use Mink67\KafkaConnect\Db\Db;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Mink67\KafkaConnect\Services\ManagerKeys;

/**
 * Perment de créer un un kafka connect
 */
class GetCert {

    /**
     * @var Db
     */
    private $db;

    /**
     * @var Crypt
     */
    private $crypt;
    /**
     * @var ManagerKeys
     */
    private $managerKeys;

    /**
     * 
     */
    public function __construct(Db $db, Crypt $crypt, ManagerKeys $managerKeys) {
        $this->db = $db;
        $this->crypt = $crypt;
        $this->managerKeys = $managerKeys;
    }


    public function __invoke()
    {
        $cert = $this->db->getClearValue("cert");

        if (is_null($cert)) {
            $cert = $this->createCert();
        }

        return $this->crypt->encrypt($cert);
    }
    /**
     * 
     */
    public function createCert()
    {
        
        
        $privateKey = $this->managerKeys->getPrivateKey();
        
        $publicKey = $this->managerKeys->getPublicKey();
        
        $payload = array(
            "iss" => "http://rna.com/api/user",
            "aud" => "http://rna.com/api",
            "iat" => time(),
            "nbf" => 1357000000,
            "channal_name" => "authority",
            "type_inst" => "authority",
            "pkey" => $publicKey,
            "uuid" => "1357000000",
        );
        
        $jwt = JWT::encode($payload, $privateKey, 'EdDSA');

        $this->db->setParam("cert", $jwt);

        return $jwt;

    }
}