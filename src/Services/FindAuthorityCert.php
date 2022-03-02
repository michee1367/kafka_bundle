<?php

namespace Mink67\KafkaConnect\Services;

use Mink67\KafkaConnect\Contracts\Crypt;
use Mink67\KafkaConnect\Db\Db;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Perment de créer un un kafka connect
 */
class FindAuthorityCert {

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
     * @var FindTokenKeycloack
     */
    private $findTokenKeycloack;


    /**
     * 
     */
    public function __construct(
        string $getAutorityCertUrl, 
        Db $db, 
        HttpClientInterface $httpClient, 
        Crypt $crypt,
        FindTokenKeycloack $findTokenKeycloack
    ) {
        $this->db = $db;
        $this->getAutorityCertUrl = $getAutorityCertUrl;
        $this->httpClient = $httpClient;
        $this->crypt = $crypt;
        $this->findTokenKeycloack = $findTokenKeycloack;
    }

    /**
     * 
     */
    public function __invoke(): string
    {
        $authorityCert = $this->getCertInDb();

        if (is_null($authorityCert)) {
            $authorityCert = $this->getCertInHttp();
        }

        return $authorityCert;
    }
    /**
     * 
     */
    private function getCertInDb(): ?string
    {
        return $this->db->getClearValue("authorityCert");
    }

    /**
     * 
     */
    private function getCertInHttp(): string
    {
        $findTokenKeycloack = $this->findTokenKeycloack;
        $tokenBearer = "Bearer ". $findTokenKeycloack();
        
        $response = $this->httpClient->request(
            'GET',
            $this->getAutorityCertUrl,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => $tokenBearer,
                ]
            ]
        );

        $content = $response->getContent();
        $objContent = json_decode($content);

        $clearText = $this->crypt->decrypt($objContent->cert);

        $this->db->setParam("authorityCert", $clearText);

        return $clearText;
    }
}