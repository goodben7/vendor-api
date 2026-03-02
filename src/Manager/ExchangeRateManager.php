<?php

namespace App\Manager;

use App\Entity\ExchangeRate;
use App\Event\ActivityEvent;
use App\Model\NewExchangeRateModel;
use App\Service\ActivityEventDispatcher;
use Doctrine\ORM\EntityManagerInterface;

class ExchangeRateManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ActivityEventDispatcher $eventDispatcher,
    ) {
    }

    public function createFrom(NewExchangeRateModel $model): ExchangeRate
    {
        $rate = new ExchangeRate();
        $rate->setBaseCurrency($model->baseCurrency);
        $rate->setTargetCurrency($model->targetCurrency);
        $rate->setBaseRate($model->baseRate ?? '0');
        $rate->setTargetRate($model->targetRate ?? '0');
        $rate->setActive($model->active ?? true);
        $rate->setCreatedAt(new \DateTimeImmutable('now'));

        $this->em->persist($rate);
        $this->em->flush();

        $this->eventDispatcher->dispatch($rate, ActivityEvent::ACTION_CREATE);

        return $rate;
    }

    public function delete(string $exchangeRateId): void
    {
        $rate = $this->em->find(ExchangeRate::class, $exchangeRateId);

        if (null === $rate) {
            throw new \InvalidArgumentException('Exchange rate not found');
        }

        if ($rate->getDeleted()) {
            throw new \InvalidArgumentException('this action is not allowed');
        }

        if($rate->isActive()) {
            throw new \InvalidArgumentException('this action is not allowed');
        }

        $rate->setDeleted(true);

        $this->em->flush();

        $this->eventDispatcher->dispatch($rate, ActivityEvent::ACTION_DELETE);
    }
}
