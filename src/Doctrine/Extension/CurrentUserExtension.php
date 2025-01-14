<?php

namespace App\Doctrine\Extension;

use App\Entity\Category;
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
     * S'applique à toutes les collections (GET /api/categories).
     */
    public function applyToCollection(
        QueryBuilder $qb,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        // On ne filtre que si la ressource correspond à Category
        if (Category::class === $resourceClass) {
            $rootAlias = $qb->getRootAliases()[0];
            $user = $this->security->getUser();

            if ($user) {
                // Filtre par l'utilisateur connecté
                $qb
                    ->andWhere(sprintf('%s.user = :current_user', $rootAlias))
                    ->setParameter('current_user', $user->getId());
            } else {
                // Si pas d'utilisateur, on renvoie 0 résultat (ou on peut lever une exception)
                $qb->andWhere('1 = 0');
            }
        }
    }

    /**
     * S'applique aux items (GET /api/categories/{id}).
     */
    public function applyToItem(
        QueryBuilder $qb,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        Operation $operation = null,
        array $context = []
    ): void {
        if (Category::class === $resourceClass) {
            $rootAlias = $qb->getRootAliases()[0];
            $user = $this->security->getUser();

            if ($user) {
                $qb
                    ->andWhere(sprintf('%s.user = :current_user', $rootAlias))
                    ->setParameter('current_user', $user->getId());
            } else {
                $qb->andWhere('1 = 0');
            }
        }
    }
}
