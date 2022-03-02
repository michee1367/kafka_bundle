<?php

namespace Mink67\KafkaConnect\Services;

use Doctrine\ORM\Mapping\GeneratedValue;
use Mink67\KafkaConnect\Contracts\Crypt;
use Mink67\KafkaConnect\Db\Db;
use Mink67\KafkaConnect\Services\Cert\GeneratePayloadCert;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Perment de créer un un kafka connect
 */
class RequestValidationCert {

    /**
     * @var Db
     */
    private $db;
    /**
     * @var string
     */
    private $getAutorityCertUrl;
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var Crypt
     */
    private $crypt;
    /**
     * @var GeneratePayloadCert
     */
    private $generatePayloadCert;
    /**
     * @var FindTokenKeycloack
     */
    private $findTokenKeycloack;


    /**
     * 
     */
    public function __construct(
        string $signCertAutorityUrl, 
        Db $db, 
        HttpClientInterface $httpClient,
        Crypt $crypt,
        GeneratePayloadCert $generatePayloadCert,
        FindTokenKeycloack $findTokenKeycloack
    ) {
        $this->db = $db;
        $this->signCertAutorityUrl = $signCertAutorityUrl;
        $this->httpClient = $httpClient;
        $this->crypt = $crypt;
        $this->generatePayloadCert = $generatePayloadCert;
        $this->findTokenKeycloack = $findTokenKeycloack;

        //CurlHttpClient
    }

    /**
     * 
     */
    public function __invoke(): string
    {
        $generatePayloadCert = $this->generatePayloadCert;

        $payload = $generatePayloadCert();

        $payloadBase64 = base64_encode(json_encode($payload));

        $cipherCert = $this->crypt->encrypt($payloadBase64);

        return $this->signCertInHttp($cipherCert);
    }

    /**
     * 
     */
    private function signCertInHttp(string $cipherPayload): string
    {
        $body = [
            'cert' => $cipherPayload
        ];


        //dd($this->httpClient);
        $findTokenKeycloack = $this->findTokenKeycloack;
        $tokenBearer = "Bearer ". $findTokenKeycloack();

        $response = $this->httpClient->request(
            'POST',
            $this->signCertAutorityUrl,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => $tokenBearer,
                ],
                'json' => $body
            ]
        );

        $content = $response->getContent();
        $objContent = json_decode($content);


        $clearText = $this->crypt->decrypt($objContent->cert);

        //$this->db->setParam("authorityCert", $clearText);
        //dd($clearText);

        return $clearText;
    }
}