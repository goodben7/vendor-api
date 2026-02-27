<?php

namespace App\Manager;

use App\Entity\Currency;
use App\Entity\Platform;
use App\Event\ActivityEvent;
use App\Storage\DataStorage;
use App\Model\NewCurrencyModel;
use App\Model\UpdateCurrencyModel;
use App\Service\ActivityEventDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;

class CurrencyManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ActivityEventDispatcher $eventDispatcher,
        private DataStorage $dataStorage,
    ) {
    }

    public function createFrom(NewCurrencyModel $model): Currency
    {
        $platformId = $this->dataStorage->getPlatformId();

        if (null === $platformId) {
            throw new UnavailableDataException('Platform not found');
        }

        $currency = new Currency();
        $currency->setCode($model->code ?? '');
        if ($model->label !== null) {
            $currency->setLabel($model->label);
        }
        if ($model->symbol !== null) {
            $currency->setSymbol($model->symbol);
        }
        $currency->setActive($model->active ?? true);
        if ($model->isDefault !== null) {
            $currency->setIsDefault($model->isDefault);
        }
        if ($currency->getIsDefault() === true) {
            $existingDefault = $this->em->getRepository(Currency::class)->findDefault();
            if ($existingDefault && $existingDefault->getPlatformId() === $platformId) {
                $existingDefault->setIsDefault(false);
            }
            $platform = $this->em->find(Platform::class, $platformId);
            if ($platform) {
                $platform->setCurrency($currency);
            }
        }
        $currency->setCreatedAt(new \DateTimeImmutable('now'));

        $this->em->persist($currency);
        $this->em->flush();

        $this->eventDispatcher->dispatch($currency, ActivityEvent::ACTION_CREATE);

        return $currency;
    }

    public function updateFrom(string $currencyId, UpdateCurrencyModel $model): Currency
    {
        $currency = $this->findCurrency($currencyId);

        if ($model->code !== null) {
            $currency->setCode($model->code);
        }
        if ($model->label !== null) {
            $currency->setLabel($model->label);
        }
        if ($model->symbol !== null) {
            $currency->setSymbol($model->symbol);
        }
        if ($model->active !== null) {
            $currency->setActive($model->active);
        }
        if ($model->isDefault !== null) {
            $currency->setIsDefault($model->isDefault);
        }
        if ($currency->getIsDefault() === true) {
            $platformId = $this->dataStorage->getPlatformId();
            if ($platformId !== null) {
                $existingDefault = $this->em->getRepository(Currency::class)->findDefault();
                if ($existingDefault && $existingDefault->getPlatformId() === $platformId && $existingDefault->getId() !== $currency->getId()) {
                    $existingDefault->setIsDefault(false);
                }
                $platform = $this->em->find(Platform::class, $platformId);
                if ($platform) {
                    $platform->setCurrency($currency);
                }
            }
        } else {
            $platformId = $this->dataStorage->getPlatformId();
            if ($platformId !== null) {
                $platform = $this->em->find(Platform::class, $platformId);
                if ($platform && $platform->getCurrency()?->getId() === $currency->getId()) {
                    $platform->setCurrency(null);
                }
            }
        }
        $currency->setUpdatedAt(new \DateTimeImmutable('now'));

        $this->em->flush();

        $this->eventDispatcher->dispatch($currency, ActivityEvent::ACTION_EDIT);

        return $currency;
    }

    private function findCurrency(string $currencyId): Currency
    {
        $currency = $this->em->find(Currency::class, $currencyId);
        if (null === $currency) {
            throw new UnavailableDataException(\sprintf('cannot find currency with id: %s', $currencyId));
        }
        return $currency;
    }
}
