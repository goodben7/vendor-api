<?php

namespace App\Service;

use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;

class CashPaymentService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function processPayment(Payment $payment): Payment
    {
        if ($payment->getMethod() === Payment::METHOD_CASH) {
            $payment->setStatus(Payment::STATUS_SUCCESS);
            $this->entityManager->flush();
        }

        return $payment;
    }

    /**
     * Validates if a payment is a cash payment
     * 
     * @param Payment $payment The payment to validate
     * @return bool Returns true if the payment method is cash, false otherwise
     */
    public function validateCashPayment(Payment $payment): bool
    {
        return $payment->getMethod() === Payment::METHOD_CASH;
    }
}