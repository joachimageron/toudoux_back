<?php

namespace App\EventListener;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Task::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Task::class)]
class TaskListener
{
    public function prePersist(Task $task, PrePersistEventArgs $args): void
    {
        if ($task->getDueDate() === null) {
            $task->setDueDate(null);
        }

        // Définir la date de création et de mise à jour
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setUpdatedAt(new \DateTimeImmutable());
    }

    public function preUpdate(Task $task, PreUpdateEventArgs $args): void
    {
        if ($task->getDueDate() === null) {
            $task->setDueDate(null);
        }

        // Définir la date de mise à jour
        $task->setUpdatedAt(new \DateTimeImmutable());
    }
}

