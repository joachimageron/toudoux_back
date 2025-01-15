<?php

namespace App\Doctrine\Extension;

use App\Entity\Category;
use App\Entity\Task;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
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
     * S'applique aux collections (GET /api/categories, GET /api/tasks, etc.).
     */
    public function applyToCollection(
        QueryBuilder                $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string                      $resourceClass,
        Operation                   $operation = null,
        array                       $context = []
    ): void {
        $user = $this->security->getUser();

        // Si pas d'utilisateur connecté, on renvoie rien ou on lève une exception
        if (!$user) {
            $queryBuilder->andWhere('1 = 0');
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        // On filtre les Category en fonction de l'utilisateur
        if (Category::class === $resourceClass) {
            $queryBuilder
                ->andWhere(sprintf('%s.user = :current_user', $rootAlias))
                ->setParameter('current_user', $user->getId());
        }
        // On filtre aussi les Task si vous rendez accessible /api/tasks
        elseif (Task::class === $resourceClass) {
            // Comme Task est relié à Category, on joint pour vérifier que Category appartient à l'utilisateur
            $queryBuilder
                ->join(sprintf('%s.category', $rootAlias), 'c')
                ->andWhere('c.user = :current_user')
                ->setParameter('current_user', $user->getId());
        }
    }

    /**
     * S'applique aux items (GET /api/categories/{id}, GET /api/tasks/{id}, etc.).
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

        // Si pas d'utilisateur connecté, on renvoie rien ou on lève une exception
        if (!$user) {
            $queryBuilder->andWhere('1 = 0');
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        // Filtre sur Category
        if (Category::class === $resourceClass) {
            $queryBuilder
                ->andWhere(sprintf('%s.user = :current_user', $rootAlias))
                ->setParameter('current_user', $user->getId());
        }
        // Filtre sur Task
        elseif (Task::class === $resourceClass) {
            $queryBuilder
                ->join(sprintf('%s.category', $rootAlias), 'c')
                ->andWhere('c.user = :current_user')
                ->setParameter('current_user', $user->getId());
        }
    }
}
