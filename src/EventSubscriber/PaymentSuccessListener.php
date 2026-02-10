<?php

namespace App\EventSubscriber;

use App\Entity\Order;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaymentSuccessListener implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Payment::EVENT_PAYMENT_CREATED => 'onPaymentCreated',
        ];
    }

    public function onPaymentCreated(Payment $payment): void
    {
        if ($payment->getStatus() === Payment::STATUS_SUCCESS) {
            $order = $payment->getOrder();
            if ($order) {
                $order->setStatus(Order::STATUS_PAID);
                $this->entityManager->flush();
            }
        }
    }
}
