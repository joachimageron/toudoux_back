<?php

namespace App\EventSubscriber;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PrePersistEventArgs;



class TaskSubscriber implements EventSubscriberInterface
{

    public function getSubscribedEvents(): array
    {
        // On déclare ici la liste des événements que l'on veut écouter
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Task) {
            return;
        }

        if ($entity->getDueDate() === null) {
            $entity->setDueDate(null);
        }

        // Définir la date de création et de mise à jour
        $entity->setCreatedAt(new \DateTimeImmutable());
        $entity->setUpdatedAt(new \DateTimeImmutable());


    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Task) {
            return;
        }

        if ($entity->getDueDate() === null) {
            $entity->setDueDate(null);
        }

        // Définir la date de mise à jour
        $entity->setUpdatedAt(new \DateTimeImmutable());
    }
}

