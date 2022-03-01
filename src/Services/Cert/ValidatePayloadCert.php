<?php

namespace Mink67\KafkaConnect\Services\Cert;

use Rakit\Validation\Validator;


/**
 * Perment de créer un un kafka connect
 */
class ValidatePayloadCert {


    /**
     * 
     */
    public function __construct() {
    }

    /**
     * 
     */
    public function __invoke(array $payload, string $certBrute = null): Cert
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