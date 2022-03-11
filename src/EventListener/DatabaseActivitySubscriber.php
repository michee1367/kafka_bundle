<?php
namespace Mink67\KafkaConnect\EventListener;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Mink67\KafkaConnect\Constant;
use Mink67\KafkaConnect\Services\Emit;

class DatabaseActivitySubscriber implements EventSubscriberInterface
{
    /**
     * @var Emit
     */
    protected $emit;

    /**
     * 
     */
    public function __construct(Emit $emit) {
        $this->emit = $emit;
    }

    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postRemove,
            Events::postUpdate,
        ];
    }

    // callback methods must be called exactly like the events they listen to;
    // they receive an argument of type LifecycleEventArgs, which gives you access
    // to both the entity object of the event and the entity manager itself
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->logActivity(Constant::CREATE_ACTION, $args);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        
        $this->logActivity(Constant::DELETE_ACTION, $args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->logActivity(Constant::UPDATE_ACTION, $args);
    }

    private function logActivity(int $action, LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible

        $emit = $this->emit;

        $emit($entity, Constant::CREATE_ACTION);

        // ... get the entity information and log it somehow
    }
}