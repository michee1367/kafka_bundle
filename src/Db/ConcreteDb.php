<?php

namespace Mink67\KafkaConnect\Db;

use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\ORM\EntityManager;
use Mink67\KafkaConnect\Contracts\ConcreteDb as Base;
use Mink67\KafkaConnect\Contracts\Crypt;
use Mink67\KafkaConnect\Contracts\Param;
use Mink67\KafkaConnect\Db\Param as DbParam;

/**
 * Perment de créer un un kafka connect
 */
class ConcreteDb implements Base {
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var string
     */
    private $tableName;

    /**
     * 
     */
    public function __construct(EntityManager $em, string $tableName) {
        $this->em = $em;
        $this->tableName = $tableName;
        
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
                    param_id int AUTO_INCREMENT,
                    param_name varchar(255),
                    param_value TEXT(2500),
                    UNIQUE (param_name),
                    PRIMARY KEY (param_id)
            )";

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeStatement([/*'tableName' => $table*/]);

    }

    private function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param $callable
     */
    private function exec($callable)
    {

    }
    /**
     * @return Param
     * @param string $key
     * @throws Exception
     * 
     */
    public function getParam(string $key): ?Param
    {
        try {            
            $param = $this->getParamLocal($key);
        } catch (TableNotFoundException $th) {
            //throw $th;
            $this->createTable();
            $param = $this->getParamLocal($key);
        }
        return $param;

    }

    /**
     * @return Param
     * @param string $key
     * @throws Exception
     * 
     */
    private function getParamLocal(string $key): ?Param
    {
        $conn = $this->getConnection();
        $table = $this->getTableName();


        $sql = " SELECT param_name, param_value FROM $table WHERE  param_name = :key";
        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery(['key' => $key]);
        
        $arrData = $resultSet->fetchAssociative();
        

        //dd($arrData);
        $param = null;

        if ($arrData && is_array($arrData) && isset($arrData["param_value"])) {
            $param = new DbParam($key, $arrData["param_value"]);

        }


        return $param;

    }
    
    /**
     * @return self
     * @param string $key
     * @param string $value
     * @throws Exception
     */
    public function setParam(string $key, $value): self
    {
        try {
            $this->setParamLocal($key, $value);
        } catch (TableNotFoundException $th) {
            
            $this->createTable();
            $this->setParamLocal($key, $value);

        }

        return $this;
        
    }
    
    /**
     * @return self
     * @param string $key
     * @param string $value
     * @throws Exception
     */
    private function setParamLocal(string $key, $value): self
    {
        $conn = $this->getConnection();
        $table = $this->getTableName();


        $sql = "INSERT INTO $table (param_name, param_value) VALUES (:name, :value)";

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeStatement([
                            'name' => $key,
                            'value' => $value,
                        ]);

        

        return $this;
        
    }
}