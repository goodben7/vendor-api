<?php

namespace App\EventSubscriber;

use App\Entity\Payment;
use Psr\Log\LoggerInterface;
use App\Service\CashPaymentService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;

class PaymentEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CashPaymentService $cashPaymentService,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Payment::EVENT_PAYMENT_CREATED => 'onPaymentCreated',
        ];
    }

    /**
     * Process payment when created
     * 
     * @param Payment $payment The event
     */
    public function onPaymentCreated(Payment $payment): void
    {
        $this->logger->info('PaymentEventSubscriber: onPaymentCreated triggered.');
        
        $this->logger->info('Processing payment.', ['payment_id' => $payment->getId()]);

        $order = $payment->getOrder();
        if ($order instanceof Order) {
            $order->setPaymentStatus(Order::PAYMENT_STATUS_PENDING);
            $this->entityManager->flush();
        }

        // Process cash payments automatically
        if ($payment->getMethod() === Payment::METHOD_CASH) {
            $this->logger->info('Processing cash payment.', ['payment_id' => $payment->getId()]);
            $this->cashPaymentService->processPayment($payment);
            $this->logger->info('Cash payment processed successfully.', ['payment_id' => $payment->getId()]);
        } else {
            $this->logger->info('Payment method is not cash, skipping automatic processing.', [
                'payment_id' => $payment->getId(),
                'method' => $payment->getMethod()
            ]);
        }
    }
}
