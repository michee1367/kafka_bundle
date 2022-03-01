<?php

namespace Mink67\KafkaConnect\Contracts;


/**
 * Perment de créer un un kafka connect
 */
interface ConcreteDb {
    
    /**
     * @return Param
     * @param string $key
     * @throws Exception
     * 
     */
    public function getParam(string $key): ?Param;
    
    /**
     * @return self
     * @param string $key
     * @param mixed $value
     * @throws Exception
     */
    public function setParam(string $key, $value): self;
    
}