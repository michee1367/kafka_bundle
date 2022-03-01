<?php

namespace Mink67\KafkaConnect\Contracts;

/**
 * Perment de créer un un kafka connect
 */
interface Param {
    
    /**
     * @return string
     * @throws Exception
     * 
     */
    public function getName(): string;
    
    /**
     * @return string
     * @throws Exception
     */
    public function getValue(): string;
    
}