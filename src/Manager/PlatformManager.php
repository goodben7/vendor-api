<?php

namespace App\Manager;

use App\Entity\Platform;
use App\Event\ActivityEvent;
use App\Entity\Currency;
use App\Model\NewPlatformModel;
use App\Model\UpdatePlatformModel;
use App\Service\ActivityEventDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;

class PlatformManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ActivityEventDispatcher $eventDispatcher,
    ) {
    }

    public function createFrom(NewPlatformModel $model): Platform
    {
        $platform = new Platform();
        $platform->setName($model->name);
        $platform->setAddress($model->address);
        $platform->setDescription($model->description);
        $platform->setCurrency($model->currency);
        if ($model->currency !== null) {
            $existingDefault = $this->em->getRepository(Currency::class)->findDefault();
            if ($existingDefault && $existingDefault->getId() !== $model->currency->getId()) {
                $existingDefault->setIsDefault(false);
            }
            $model->currency->setIsDefault(true);
        }
        $platform->setPhone($model->phone);
        $platform->setEmail($model->email);
        $platform->setAllowTableManagement($model->allowTableManagement ?? true);
        $platform->setAllowOnlineOrder($model->allowOnlineOrder ?? false);
        $platform->setPaymentConfigJson($model->paymentConfigJson);
        $platform->setActive($model->active ?? true);
        $platform->setCreatedAt(new \DateTimeImmutable('now'));

        $this->em->persist($platform);
        $this->em->flush();

        $this->eventDispatcher->dispatch($platform, ActivityEvent::ACTION_CREATE);

        return $platform;
    }

    public function updateFrom(string $platformId, UpdatePlatformModel $model): Platform
    {
        $platform = $this->findPlatform($platformId);

        $platform->setName($model->name);
        $platform->setAddress($model->address);
        $platform->setDescription($model->description);
        $platform->setCurrency($model->currency);
        if ($model->currency !== null) {
            $existingDefault = $this->em->getRepository(Currency::class)->findDefault();
            if ($existingDefault && $existingDefault->getId() !== $model->currency->getId()) {
                $existingDefault->setIsDefault(false);
            }
            $model->currency->setIsDefault(true);
        }
        $platform->setPhone($model->phone);
        $platform->setEmail($model->email);
        if ($model->allowTableManagement !== null) {
            $platform->setAllowTableManagement($model->allowTableManagement);
        }
        if ($model->allowOnlineOrder !== null) {
            $platform->setAllowOnlineOrder($model->allowOnlineOrder);
        }
        $platform->setPaymentConfigJson($model->paymentConfigJson);
        if ($model->active !== null) {
            $platform->setActive($model->active);
        }
        $platform->setUpdatedAt(new \DateTimeImmutable('now'));

        $this->em->flush();

        $this->eventDispatcher->dispatch($platform, ActivityEvent::ACTION_EDIT);

        return $platform;
    }

    private function findPlatform(string $platformId): Platform
    {
        $platform = $this->em->find(Platform::class, $platformId);
        if (null === $platform) {
            throw new UnavailableDataException(sprintf('cannot find platform with id: %s', $platformId));
        }
        return $platform;
    }
}
