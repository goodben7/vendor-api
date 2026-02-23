<?php

namespace App\Doctrine;

use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Metadata\Operation;
use App\Model\RemoveInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;

class RemoveDeletedInterfaceExtension implements QueryCollectionExtensionInterface
{
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->restrict($queryBuilder, $resourceClass);
    }

    private function restrict(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $interfaces = \class_implements($resourceClass) ?: [];
        if (!isset($interfaces[RemoveInterface::class]) && !\method_exists($resourceClass, 'getDeleted')) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(\sprintf('(%1$s.deleted IS NULL OR %1$s.deleted = :deletedFalse)', $rootAlias));
        $queryBuilder->setParameter('deletedFalse', false);
    }
}
