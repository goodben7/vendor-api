<?php
namespace App\EventSubscriber;


use Doctrine\ORM\Events;
use App\Storage\DataStorage;
use App\Exception\UnavailableDataException;
use App\Contract\PlatformCentricInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use App\Entity\User;


#[AsDoctrineListener(event: Events::prePersist)]
class InjectPlatformEventSubscriber  {
    
    public function __construct(private DataStorage $storage)
    {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $object = $args->getObject();
        if (!$object instanceof PlatformCentricInterface) {
            return;
        }

        $platformId = $this->storage->getPlatformId();
        if (null === $platformId) {
            if ($object instanceof User) {
                return;
            }
            throw new UnavailableDataException('Platform not found');
        }

        /** @var PlatformCentricInterface $entity */
        $entity = $object;
        $entity->setPlatformId($platformId);
    }
}
