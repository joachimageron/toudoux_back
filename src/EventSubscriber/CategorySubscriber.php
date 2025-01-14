<?php

namespace App\EventSubscriber;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Bundle\SecurityBundle\Security;
use Cocur\Slugify\Slugify;


class CategorySubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

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

        if (!$entity instanceof Category) {
            return;
        }

        // Associer la catégorie à l’utilisateur connecté (si user est non null)
        $user = $this->security->getUser();
        $entity->setUser($user);

        // Définir une valeur pour slug, pour éviter qu'il soit null
        $slugify = new Slugify();
        $slug = $slugify->slugify($entity->getName() . '-' . $user->getId());
        $entity->setSlug($slug);

        // Définir la date de création et de mise à jour
        $entity->setCreatedAt(new \DateTimeImmutable());
        $entity->setUpdatedAt(new \DateTimeImmutable());
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Category) {
            return;
        }

        $user = $this->security->getUser();

        // Définir une valeur pour slug, pour éviter qu'il soit null
        $slugify = new Slugify();
        $slug = $slugify->slugify($entity->getName() . '-' . $user->getId());
        $entity->setSlug($slug);

        // Définir la date de mise à jour
        $entity->setUpdatedAt(new \DateTimeImmutable());
    }
}
