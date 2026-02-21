<?php
namespace App\EventSubscriber;


use Doctrine\ORM\Events;
use App\Storage\DataStorage;
use App\Contract\PlatformCentricInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;


#[AsDoctrineListener(event: Events::prePersist)]
class InjectPlatformEventSubscriber  {
    
    public function __construct(private DataStorage $storage)
    {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        if (null != $this->storage->getPlatformId()) {
            if ($args->getObject() instanceof PlatformCentricInterface) {
                /** @var PlatformCentricInterface */
                $e = $args->getObject();

                $e->setPlatformId($this->storage->getPlatformId());
            }
        }
    }
}