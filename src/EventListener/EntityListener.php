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