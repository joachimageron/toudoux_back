<?php

namespace App\EventSubscriber;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

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
        ];
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Category) {
            return;
        }

        // Définir une valeur pour slug, pour éviter qu'il soit null
        $entity->setSlug('coucou');

        // Définir la date de création et de mise à jour
        $entity->setCreatedAt(new \DateTimeImmutable());
        $entity->setUpdatedAt(new \DateTimeImmutable());

        // Associer la catégorie à l’utilisateur connecté (si user est non null)
        $user = $this->security->getUser();
        if ($user) {
            $entity->setUser($user);
        }
    }
}
