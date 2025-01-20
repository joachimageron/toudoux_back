<?php

namespace App\Doctrine\Extension;

use App\Entity\Category;
use App\Entity\Task;
use App\Entity\ImportData;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * S'applique aux collections (GET /api/categories, GET /api/tasks, GET /api/import_datas, etc.).
     */
    public function applyToCollection(
        QueryBuilder                $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string                      $resourceClass,
        Operation                   $operation = null,
        array                       $context = []
    ): void {
        $user = $this->security->getUser();

        // Si pas d'utilisateur connecté, on ne renvoie rien
        if (!$user) {
            $queryBuilder->andWhere('1 = 0');
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        // Filtre pour Category et ImportData
        if (Category::class === $resourceClass || ImportData::class === $resourceClass) {
            $queryBuilder
                ->andWhere(sprintf('%s.user = :current_user', $rootAlias))
                ->setParameter('current_user', $user->getId());
        }
        // Filtre pour Task
        elseif (Task::class === $resourceClass) {
            // Task est relié à Category, donc on vérifie la Category liée
            $queryBuilder
                ->join(sprintf('%s.category', $rootAlias), 'c')
                ->andWhere('c.user = :current_user')
                ->setParameter('current_user', $user->getId());
        }
        elseif (User::class === $resourceClass) {
            if (!$this->security->isGranted('ROLE_ADMIN')) {
                $queryBuilder
                    ->andWhere(sprintf('%s.id = :current_user', $rootAlias))
                    ->setParameter('current_user', $user->getId());
            }
        }
    }

    /**
     * S'applique aux items (GET /api/categories/{id}, GET /api/tasks/{id}, GET /api/import_datas/{id}, etc.).
     */
    public function applyToItem(
        QueryBuilder                $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string                      $resourceClass,
        array                       $identifiers,
        Operation                   $operation = null,
        array                       $context = []
    ): void {
        $user = $this->security->getUser();

        // Si pas d'utilisateur connecté, on ne renvoie rien
        if (!$user) {
            $queryBuilder->andWhere('1 = 0');
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        // Filtre pour Category et ImportData
        if (Category::class === $resourceClass || ImportData::class === $resourceClass) {
            $queryBuilder
                ->andWhere(sprintf('%s.user = :current_user', $rootAlias))
                ->setParameter('current_user', $user->getId());
        }
        // Filtre pour Task
        elseif (Task::class === $resourceClass) {
            $queryBuilder
                ->join(sprintf('%s.category', $rootAlias), 'c')
                ->andWhere('c.user = :current_user')
                ->setParameter('current_user', $user->getId());
        }
        elseif (User::class === $resourceClass) {
            if (!$this->security->isGranted('ROLE_ADMIN')) {
                $queryBuilder
                    ->andWhere(sprintf('%s.id = :current_user', $rootAlias))
                    ->setParameter('current_user', $user->getId());
            }
        }
    }
}
