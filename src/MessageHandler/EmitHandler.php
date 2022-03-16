<?php

namespace Mink67\KafkaConnect\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use Mink67\KafkaConnect\Message\EmitMessage;
use Mink67\KafkaConnect\Services\Emit;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Perment de créer un un kafka connect
 */
class EmitHandler implements MessageHandlerInterface {

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Emit
     */
    private $emit;

    /**
     * @param EntityManagerInterface $em
     * @param Emit $emit
     */
    public function __construct(EntityManagerInterface $em, Emit $emit) {
        $this->em = $em;
        $this->emit = $emit;
    }

    /**
     * 
     */
    public function __invoke(EmitMessage $message)
    {
        $entity = $this->em->find($message->getClassName(), $message->getEntityId());

        if (!is_null($entity)) {
           $emit = $this->emit;
           $emit($entity, $message->getActionType());
        }
    }

}