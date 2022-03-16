<?php

namespace Mink67\KafkaConnect\Message;

/**
 * Perment de créer un un kafka connect
 */
class EmitMessage {
    /**
     * @var int
     */
    private $entityId;
    /**
     * @var int
     */
    private $actionType;
    /**
     * @var string
     */
    private $className;

    /**
     * @param int $entityId
     * @param int $actionType
     * @param string $className
     */
    public function __construct(int $entityId, string $className, int $actionType) {
        $this->entityId = $entityId;

        if (!class_exists($className)) {
           throw new  \InvalidArgumentException("$className is not class", 1);
        }
        $this->className = $className;
        $this->actionType = $actionType;
    }


    /**
     * Get the value of entityId
     *
     * @return  int
     */ 
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Get the value of actionType
     *
     * @return  int
     */ 
    public function getActionType()
    {
        return $this->actionType;
    }

    /**
     * Get the value of className
     *
     * @return  string
     */ 
    public function getClassName()
    {
        return $this->className;
    }

}