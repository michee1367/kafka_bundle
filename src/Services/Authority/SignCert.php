<?php

namespace Mink67\KafkaConnect\Services\Authority;

use Mink67\KafkaConnect\Contracts\Crypt;
use Mink67\KafkaConnect\Db\Db;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Mink67\KafkaConnect\Services\Cert\Cert;
use Mink67\KafkaConnect\Services\Cert\ValidatePayloadCert;
use Mink67\KafkaConnect\Services\ManagerKeys;
use Symfony\Component\Uid\Uuid;

/**
 * Perment de créer un un kafka connect
 */
class SignCert {


    /**
     * @var Crypt
     */
    private $crypt;
    /**
     * @var ManagerKeys
     */
    private $managerKeys;
    /**
     * @var ValidatePayloadCert
     */
    private $validatePayloadCert;

    /**
     * 
     */
    public function __construct(Crypt $crypt, ManagerKeys $managerKeys, ValidatePayloadCert $validatePayloadCert) {
        $this->crypt = $crypt;
        $this->managerKeys = $managerKeys;
        $this->validatePayloadCert = $validatePayloadCert;
    }

    /**
     * 
     */
    public function __invoke(string $certCipher)
    {
        $certBase64EnCode = $this->crypt->decrypt($certCipher);

        $certPayload = json_decode(base64_decode($certBase64EnCode), true);
        $validatePayloadCert = $this->validatePayloadCert;

        $cert = $validatePayloadCert($certPayload);

        if (! $cert instanceof Cert) {
            throw new \Exception("Invalid certification");
            
        }


        $pKey = $this->managerKeys->getPrivateKey();
        $uuid = Uuid::v6();


        $payload = array(
            "iss" => "http://rna.com/api/user",
            "aud" => "http://rna.com/api",
            "iat" => time(),
            "nbf" => 1357000000,
            "channal_name" => $cert->getChannelName(),
            "type_inst" => $cert->getTypeInst(),
            "pkey" => $cert->getPkey(),
            "uuid" => (string) $uuid,
        );

        $jwt = JWT::encode($payload, $pKey, 'EdDSA');

        //return $jwt;

        return $this->crypt->encrypt($jwt);
        
    }
}