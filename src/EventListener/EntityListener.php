<?php


namespace App\EventListener;


use App\Utility\MercureSender;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;

class EntityListener {
    private $sender;

    public function __construct(MercureSender $sender) {
        $this->sender = $sender;
    }

    public function onFlush(OnFlushEventArgs $args) {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        foreach ($uow->getScheduledEntityUpdates() as $entity)
        file_put_contents('php://stderr', print_r($entity->toArray(), TRUE));
        file_put_contents('php://stderr', print_r("\n", TRUE));
       // $this->sender->sendUpdate($event->getEntity()->getClassname(), "geen idee", $event->getEntity()->toArray());
    }

    public function postUpdate(LifecycleEventArgs $event) {
        $this->sender->sendUpdate($event->getEntity()->getClassname(), MercureSender::METHOD_PATCH, $event->getEntity()->toArray());
    }

    public function postPersist(LifecycleEventArgs $event) {
        if ('cli' != php_sapi_name()) {
            $this->sender->sendUpdate($event->getEntity()->getClassname(), MercureSender::METHOD_ADD, $event->getEntity()->toArray());
        }
    }

    public function postRemove(LifecycleEventArgs $event) {
        $this->sender->sendUpdate($event->getEntity()->getClassname(), MercureSender::METHOD_DELETE, $event->getEntity()->toArray());
    }
}