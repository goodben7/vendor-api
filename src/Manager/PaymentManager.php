<?php

namespace App\Manager;

use App\Entity\Payment;
use App\Model\NewPaymentModel;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\InvalidActionInputException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PaymentManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
        private PaymentRepository $paymentRepository
    ) {
    }

    public function createPayment(NewPaymentModel $model): Payment
    {
        if ($model->amount !== $model->order->getTotalAmount()) {
            throw new InvalidActionInputException('Payment amount must match order total.');
        }

        if ($this->paymentRepository->findSuccessfulForPaidOrder($model->order)) {
            throw new InvalidActionInputException('Payment already exists for this order.');
        }

        $payment = new Payment();
        $payment->setOrder($model->order);
        $payment->setAmount($model->amount);
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
