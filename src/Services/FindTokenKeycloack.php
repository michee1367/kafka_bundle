<?php

namespace Mink67\KafkaConnect\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Perment de créer un un kafka connect
 */
class FindTokenKeycloack {

    /**
     * @var string
     */
    private $keycloackAppUserName;
    /**
     * @var string
     */
    private $keycloackAppPassword;
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var string
     */
    private $keycloackAppClientSecret;
    /**
     * @var string
     */
    private $keycloackAppClientId;
    /**
     * @var string
     */
    private $keycloackAuthUrl;


    /**
     * 
     */
    public function __construct(
        string $keycloackAppUserName, 
        string $keycloackAppPassword, 
        string $keycloackAppClientSecret, 
        string $keycloackAppClientId, 
        string $keycloackAuthUrl, 
        HttpClientInterface $httpClient, 
        
    ) {
        $this->keycloackAppUserName = $keycloackAppUserName;
        $this->keycloackAppPassword = $keycloackAppPassword;
        $this->httpClient = $httpClient;
        $this->keycloackAppClientSecret = $keycloackAppClientSecret;
        $this->keycloackAppClientId = $keycloackAppClientId;
        $this->keycloackAuthUrl = $keycloackAuthUrl;
    }


    /**
     * 
     */
    public function __invoke(): string
    {
        $response = $this->httpClient->request('POST', $this->keycloackAuthUrl, [
            // defining data using a regular string
        
            // defining data using an array of parameters
            'body' => [
                'username' => $this->keycloackAppUserName, 
                'password' => $this->keycloackAppPassword, 
                'client_secret' => $this->keycloackAppClientSecret, 
                'grant_type' => 'password', 
                'client_id' => $this->keycloackAppClientId, 
            ],
        ]);

        $content = $response->getContent();
        $objContent = json_decode($content);

        return $objContent->access_token;
    }
}