<?php

namespace App\Manager;

use App\Entity\Tablet;
use App\Model\NewTabletModel;
use App\Model\UpdateTabletModel;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;
use App\Event\ActivityEvent;
use App\Service\ActivityEventDispatcher;

class TabletManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ActivityEventDispatcher $eventDispatcher,
    ) {
    }

    public function createFrom(NewTabletModel $model): Tablet
    {
        $tablet = new Tablet();
        if ($model->platformTable !== null) {
            $tablet->setPlatformTable($model->platformTable);
        }
        $tablet->setLabel($model->label);
        $tablet->setDeviceId($model->deviceId);
        $tablet->setLastHeartbeat($model->lastHeartbeat);
        $tablet->setActive($model->active ?? true);
        $tablet->setCreatedAt(new \DateTimeImmutable('now'));

        $this->em->persist($tablet);
        $this->em->flush();

        $this->eventDispatcher->dispatch($tablet, ActivityEvent::ACTION_CREATE);

        return $tablet;
    }

    public function updateFrom(string $tabletId, UpdateTabletModel $model): Tablet
    {
        $tablet = $this->findTablet($tabletId);
        if ($model->platformTable !== null) {
            $tablet->setPlatformTable($model->platformTable);
        }
        $tablet->setLabel($model->label);
        $tablet->setDeviceId($model->deviceId);
        if ($model->lastHeartbeat !== null) {
            $tablet->setLastHeartbeat($model->lastHeartbeat);
        }
        if ($model->active !== null) {
            $tablet->setActive($model->active);
        }
        $tablet->setUpdatedAt(new \DateTimeImmutable('now'));

        $this->em->flush();

        $this->eventDispatcher->dispatch($tablet, ActivityEvent::ACTION_EDIT);

        return $tablet;
    }

    private function findTablet(string $tabletId): Tablet
    {
        $tablet = $this->em->find(Tablet::class, $tabletId);
        if (null === $tablet) {
            throw new UnavailableDataException(sprintf('cannot find tablet with id: %s', $tabletId));
        }
        return $tablet;
    }
}
