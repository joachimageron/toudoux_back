<?php

namespace App\EventListener;

use App\Entity\Task;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

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

