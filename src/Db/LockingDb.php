<?php

namespace Mink67\KafkaConnect\Db;

use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Mink67\KafkaConnect\Contracts\Crypt;
use Mink67\KafkaConnect\Contracts\Param;
use Mink67\KafkaConnect\Db\Param as DbParam;

/**
 * Perment de créer un un kafka connect
 */
class LockingDb {
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var string
     */
    private $tableName;

    /**
     * 
     */
    public function __construct(EntityManagerInterface $em, string $prefixTableName) {
        $this->em = $em;
        $this->prefixTableName = $prefixTableName;
        
    }
    /**
     * 
     */
    private function getConnection()
    {
        $em = $this->em;

        $conn = $em->getConnection();

        return $conn;
    }
    /**
     * 
     */
    private function createTable()
    {
        $table = $this->getTableName();

        $conn = $this->getConnection();

        $sql = " CREATE TABLE IF NOT EXISTS  $table (
                    lock_id int AUTO_INCREMENT,
                    entity_iri varchar(255),
                    UNIQUE (entity_iri),
                    PRIMARY KEY (lock_id)
            )";

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeStatement([/*'tableName' => $table*/]);

    }

    private function getTableName()
    {
        return $this->tableName . "_loking";
    }

    /**
     * @param $callable
     */
    private function exec($callable)
    {

    }
    /**
     * @return Lock
     * @param string $key
     * @throws Exception
     * 
     */
    public function getLock(string $key): ?Lock
    {
        try {            
            $param = $this->getLockLocal($key);
        } catch (TableNotFoundException $th) {
            //throw $th;
            $this->createTable();
            $param = $this->getLockLocal($key);
        }
        return $param;

    }

    /**
     * @return Lock
     * @param string $key
     * @throws Exception
     * 
     */
    private function getLockLocal(string $key): ?Lock
    {
        $conn = $this->getConnection();
        $table = $this->getTableName();

        $sql = " SELECT entity_iri FROM $table WHERE  entity_iri = :key";
        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery(['key' => $key]);
        
        $arrData = $resultSet->fetchAssociative();
        
        //dd($arrData);
        $param = null;

        if ($arrData && is_array($arrData) && isset($arrData["entity_iri"])) {
            $param = new Lock($key);

        }

        return $param;

    }
    
    /**
     * @return self
     * @param string $key
     * @param string $value
     * @throws Exception
     */
    public function setLock(string $iri): self
    {
        try {
            
            $this->setLockLocal($iri);
        } catch (TableNotFoundException $th) {
            
            $this->createTable();
            $this->setLockLocal($iri);

        }

        return $this;
        
    }
    
    /**
     * @return self
     * @param string $key
     * @param string $value
     * @throws Exception
     */
    private function setLockLocal(string $iri): self
    {
        $conn = $this->getConnection();
        $table = $this->getTableName();


        $sql = "INSERT INTO $table (entity_iri) VALUES (:iri)";

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeStatement([
                            'iri' => $iri,
                        ]);

        

        return $this;
        
    }

    /**
     * @return self
     * @param string $key
     * @param string $value
     * @throws Exception
     */
    private function removeLockLocal(string $iri): self
    {
        $conn = $this->getConnection();
        $table = $this->getTableName();


        $sql = "DELETE FROM $table WHERE  entity_iri = :iri";

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeStatement([
                            'iri' => $iri,
                        ]);

        

        return $this;
        
    }
    /**
     * @return self
     * @param string $key
     * @param string $value
     * @throws Exception
     */
    public function removeLock(string $iri): self
    {
        try {
            
            $this->removeLockLocal($iri);
        } catch (TableNotFoundException $th) {
            
            $this->createTable();
            $this->removeLockLocal($iri);

        }

        return $this;
        
    }
}