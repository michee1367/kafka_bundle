<?php

namespace Mink67\KafkaConnect\Services;

use ApiPlatform\Core\Api\IriConverterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mink67\KafkaConnect\Annotations\Readers\ReaderConfig;
use Mink67\KafkaConnect\Constant;
use Mink67\KafkaConnect\Db\LockingDb;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Mink67\KafkaConnect\Services\Utils\MessageDbValidator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Perment de créer un un kafka connect
 */
class EntityManager {
    
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var IriConverterInterface
     */
    private $iriConverter;
    /**
     * @var LockingDb
     */
    private $db;

    /**
     * 
     */
    public function __construct(
        EntityManagerInterface $em, 
        HttpClientInterface $httpClient,
        IriConverterInterface $iriConverter,
        LockingDb $db
    ) {
        $this->httpClient = $httpClient;
        $this->em = $em;
        $this->iriConverter = $iriConverter;
        $this->db = $db;
    }

    /**
     * 
     */
    public function getEntityPersist($entity)
    {
        if (
            !method_exists($entity, "getId")
        ) {
            return null;
        }

        $exception = null;
        $newEntity = null;

        $this->unlock($entity);

        for ($i=0; $i < 3; $i++) {
            
            try {
                $this->lock($entity);
                
                $newEntity = $this->getEntityPersistLocal($entity);
                $exception = null;

                $this->unlock($entity);
                
            } catch (\Throwable $th) {
                $exception = $th;
                $this->unlock($entity);
            }

        }

        if (!is_null($exception)) {
            throw $exception;
        }

        return $newEntity;
    }

    /**
     * 
     */
    private function getEntityPersistLocal($entity)
    {
        $repository = $this->em->getRepository(get_class($entity));

        $newEntity = $repository->find($entity->getId());

        if (is_null($newEntity)) {
            $this->em->persist($entity);
            $newEntity = $entity;
        }

        return $newEntity;
    }
    
    /**
     * 
     */
    public function lock($entity)
    {
        $iri = $this->iriConverter->getIriFromResourceClass(get_class($entity))."/".$entity->getId();

        $this->db->setLock($iri);
    }
    /**
     * 
     */
    public function unlock($entity)
    {
        $iri = $this->iriConverter->getIriFromResourceClass(get_class($entity))."/".$entity->getId();

        $this->db->removeLock($iri);        
    }

    /**
     * 
     */
    public function getLock($entity)
    {
        $iri = $this->iriConverter->getIriFromResourceClass(get_class($entity))."/".$entity->getId();

        return $this->db->getLock($iri);        
    }

    

}