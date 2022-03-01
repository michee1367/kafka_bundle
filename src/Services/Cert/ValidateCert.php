<?php

namespace Mink67\KafkaConnect\Services\Cert;

use Rakit\Validation\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Perment de créer un un kafka connect
 */
class ValidateCert {

    /**
     * @var ValidateCertBrute
     */
    private $validateBrute;

    /**
     * 
     */
    public function __construct(ValidateCertBrute $validateBrute) {
        $this->validateBrute = $validateBrute;
    }

    /**
     * 
     */
    public function __invoke(string $authorityCertBrute, string $certBrute): Cert
    {  
        $validateBrute = $this->validateBrute;
        //$cert = $this->getValidateCertBrute($certBrute);
        $cert = $validateBrute($certBrute);
        //$authorityCert = $this->getValidateCertBrute($authorityCertBrute);
        $authorityCert = $validateBrute($authorityCertBrute);

        return $this->validateCert($cert, $authorityCert->getPkey());

    }
    /**
     * @param string $certBrute
     * 
     */
    private function getValidateCertBrute(string $certBrute): Cert
    {
        $validator = new Validator;
        $payloadBrute = explode(".", $certBrute)[1];
        $payload = json_decode(base64_decode($payloadBrute), true);

        $validation = $validator->validate(
            $payload,
            array(
                "channal_name" => "required",
                "type_inst" => "required",
                "pkey" => "required"
            )
        );

        if ($validation->fails()) {
            throw new \Exception("Cert payload invalid");
        }
        return new Cert(
            $payload["channal_name"],
            $payload["type_inst"],
            $payload["pkey"],
            isset($payload["uuid"])? $payload["uuid"] : "",
            $certBrute,
        );
    }

    /**
     * 
     */
    private function validateCert(Cert $cert, string $authorityPKey): Cert
    {        
        $decoded = JWT::decode($cert->getBrute(), new Key($authorityPKey, 'EdDSA'));

        return $cert;
    }
}