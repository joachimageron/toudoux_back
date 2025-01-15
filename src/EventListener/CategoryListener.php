<?php

namespace App\EventListener;

use App\Entity\Category;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

class CategoryListener
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Méthode appelée avant l'insertion d'une Category en base (équivalent prePersist).
     */
    public function prePersist(Category $category, PrePersistEventArgs $event): void
    {
        // Associer la catégorie à l’utilisateur connecté (si user est non null)
        $user = $this->security->getUser();
        $category->setUser($user);

        // Génération du slug
        $slugify = new Slugify();
        $slug = $slugify->slugify($category->getName() . '-' . $user->getId());
        $category->setSlug($slug);

        // Définir les dates de création et de mise à jour
        $category->setCreatedAt(new \DateTimeImmutable());
        $category->setUpdatedAt(new \DateTimeImmutable());
    }

    /**
     * Méthode appelée avant la mise à jour d'une Category (équivalent preUpdate).
     */
    public function preUpdate(Category $category, PreUpdateEventArgs $event): void
    {
        $user = $this->security->getUser();

        // Génération du slug
        $slugify = new Slugify();
        $slug = $slugify->slugify($category->getName() . '-' . $user->getId());
        $category->setSlug($slug);

        // Mettre à jour la date
        $category->setUpdatedAt(new \DateTimeImmutable());
    }
}
