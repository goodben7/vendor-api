<?php

namespace App\Manager;

use App\Entity\Payment;
use App\Entity\Platform;
use App\Storage\DataStorage;
use App\Model\NewPaymentModel;
use App\Service\CurrencyConverter;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;
use App\Exception\InvalidActionInputException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PaymentManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
        private PaymentRepository $paymentRepository,
        private DataStorage $dataStorage,
        private CurrencyConverter $service
    ) {
    }

    public function createPayment(NewPaymentModel $model): Payment
    {
        $platformId = $this->dataStorage->getPlatformId();

        if (null === $platformId) {
            throw new UnavailableDataException('Platform not found');
        }

        $platform = $this->entityManager->find(Platform::class, $platformId);

        if (null === $platform) {
            throw new UnavailableDataException('Platform not found');
        }

        if (null === $platform->getCurrency()) {
            throw new InvalidActionInputException('Platform currency must be set.');
        }

        if ($this->paymentRepository->findSuccessfulForPaidOrder($model->order)) {
            throw new InvalidActionInputException('Payment already exists for this order.');
        }

        $restaurantCurrency = $platform->getCurrency();
        $paidCurrency = $model->currency;

        if (bccomp($model->amount, $model->order->getTotalAmount(), 2) !== 0) {
            throw new InvalidActionInputException('Payment amount does not match order total.');
        }

        if ($paidCurrency->getId() !== $restaurantCurrency->getId()) {
            $paidAmount = $this->service->convert(
                $restaurantCurrency,
                $paidCurrency,
                $model->amount
            );
        } else {
            $paidAmount = $model->amount;
        }
        $rateUsed = $this->service->getRate($restaurantCurrency, $paidCurrency);

        $payment = new Payment();
        $payment->setOrder($model->order);
        $payment->setAmount($model->amount);
        $payment->setCurrency($restaurantCurrency);
        $payment->setPaidAmount($paidAmount);
        $payment->setPaidCurrency($paidCurrency);
        $payment->setExchangeRateUsed($rateUsed);
        $payment->setMethod($model->method);
        $payment->setProvider($model->provider);
        $payment->setTransactionRef($model->transactionRef);
        $payment->setPaidAt(new \DateTimeImmutable());

        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch($payment, Payment::EVENT_PAYMENT_CREATED);

        return $payment;
    }
}
