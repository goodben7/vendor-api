<?php

namespace App\Doctrine;

use App\Entity\User;
use App\Storage\DataStorage;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Metadata\Operation;
use Symfony\Bundle\SecurityBundle\Security;
use App\Contract\PlatformRestrictiveInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;

class PlatformRestrictionExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface {

    public function __construct(
        private DataStorage $storage,
        private Security $security,
        private ?LoggerInterface $logger = null
    )
    {
        if ($this->logger) {
            $this->logger->debug('PlatformRestrictionExtension::__construct - Extension initialized');  
        }
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        if ($this->logger) {
            $this->logger->debug('PlatformRestrictionExtension::applyToCollection - Called for resource', [
                'resourceClass' => $resourceClass,
                'operation' => $operation ? get_class($operation) : 'null',
                'context' => array_keys($context)
            ]);
        }
        $this->restrict($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
        if ($this->logger) {
            $this->logger->debug('PlatformRestrictionExtension::applyToItem - Called for resource', [
                'resourceClass' => $resourceClass,
                'identifiers' => $identifiers,
                'operation' => $operation ? \get_class($operation) : 'null',
                'context' => array_keys($context)
            ]);
        }
        $this->restrict($queryBuilder, $resourceClass);
    }

    private function restrict(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if ($this->logger) {
            $this->logger->debug('PlatformRestrictionExtension::restrict - Starting restriction process', [
                'resourceClass' => $resourceClass,
                'queryBuilder' => \get_class($queryBuilder),
                'rootAliases' => $queryBuilder->getRootAliases()
            ]);
        }
        
        $platformId = $this->getPlatformIdFromStorageOrUser();
        
        if ($this->logger) {
            $this->logger->info('PlatformRestrictionExtension::restrict - Platform ID check', [
                'platformId' => $platformId,
                'resourceClass' => $resourceClass,
                'hasPlatformId' => $platformId !== null ? 'yes' : 'no',
                'storage_class' => \get_class($this->storage)
            ]);
        }
        
        if (null == $platformId) {
            if ($this->logger) {
                $this->logger->warning('PlatformRestrictionExtension::restrict - No platform ID found in storage or from authenticated user');
            }
            return;
        }

        $interfaces = \class_implements($resourceClass);
        if (!isset($interfaces[PlatformRestrictiveInterface::class])) {
            if ($this->logger) {
                $this->logger->info('PlatformRestrictionExtension::restrict - Class does not implement PlatformRestrictiveInterface', [ 
                    'resourceClass' => $resourceClass
                ]);
            }
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        
        // Use 'id' field for Platform entity, 'platformId' for other entities
        if ($resourceClass === 'App\\Entity\\Platform') {
            $queryBuilder->andWhere(\sprintf('%s.id = :pid', $rootAlias));
            
            if ($this->logger) {
                $this->logger->debug('PlatformRestrictionExtension::restrict - Using ID field for Platform entity');    
            }
        } else {
            $queryBuilder->andWhere(\sprintf('%s.platformId = :pid', $rootAlias));
            
            if ($this->logger) {
                $this->logger->debug('PlatformRestrictionExtension::restrict - Using platformId field for entity');
            }
        }
        
        $queryBuilder->setParameter('pid', $platformId);
        
        if ($this->logger) {
            $this->logger->info('PlatformRestrictionExtension::restrict - Applied platform restriction', [
                'platformId' => $platformId,    
                'resourceClass' => $resourceClass,
                'rootAlias' => $rootAlias,
                'query_dql' => $queryBuilder->getDQL(),
                'query_parameters' => array_map(function($param) {
                    return ['name' => $param->getName(), 'value' => $param->getValue(), 'type' => $param->getType()];
                }, $queryBuilder->getParameters()->toArray())
            ]);
        }
    }
    
    /**
     * Attempts to get the owner ID from storage first, then from the authenticated user if not available
     */
    private function getPlatformIdFromStorageOrUser(): ?string
    {
        // First try to get platform ID from storage
        $platformId = $this->storage->getPlatformId();
        
        // If not available in storage, try to get it from the authenticated user
        if (null === $platformId) {
            /** @var User|null $user */
            $user = $this->security->getUser();
            
            if ($this->logger) {
                $this->logger->debug('PlatformRestrictionExtension::getPlatformIdFromStorageOrUser - Attempting to get platform ID from user', [
                    'is_authenticated' => $user !== null,
                    'user_class' => $user ? \get_class($user) : 'null',
                    'user_id' => $user?->getId(),
                    'has_platform_id' => $user?->getPlatformId() ? 'yes' : 'no',
                    'platform_id' => $user?->getPlatformId()
                ]);
            }
            
            if ($user instanceof User && $user->getPlatformId()) {
                $platformId = $user->getPlatformId();
                
                if ($this->logger) {
                    $this->logger->info('PlatformRestrictionExtension::getPlatformIdFromStorageOrUser - Retrieved platform ID from authenticated user', [
                        'platform_id' => $platformId,
                        'user_id' => $user->getId()
                    ]);
                }
            }
        } else if ($this->logger) {
            $this->logger->debug('PlatformRestrictionExtension::getPlatformIdFromStorageOrUser - Using platform ID from storage', [
                'platform_id' => $platformId
            ]);
        }
        
        return $platformId;
    }
}