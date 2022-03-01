<?php

namespace Mink67\KafkaConnect\Services\Cert;

use Rakit\Validation\Validator;

/**
 * Perment de créer un un kafka connect
 */
class ValidateCertBrute {

    /**
     * @var ValidatePayloadCert
     */
    private $validatePayloadCert;

    /**
     * 
     */
    public function __construct(ValidatePayloadCert $validatePayloadCert) {
        $this->validatePayloadCert = $validatePayloadCert;
    }

    /**
     * @param string $certBrute
     * 
     */
    public function __invoke(string $certBrute): Cert
    {
        $payloadBrute = explode(".", $certBrute)[1];
        $payload = json_decode(base64_decode($payloadBrute), true);
        $validatePayloadCert = $this->validatePayloadCert;
        return $validatePayloadCert($payload, $certBrute);
    }
    /**
     * 
     */
    private function validatePayloadCert(array $payload, string $certBrute = null): Cert
    {
        $validator = new Validator;

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
}