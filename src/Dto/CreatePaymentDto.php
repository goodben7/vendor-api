<?php

namespace App\Dto;

use App\Entity\Order;
use App\Entity\Payment;
use App\Entity\Currency;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePaymentDto
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\NotBlank]
        public readonly Order $order,

        #[Assert\NotNull]
        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly string $amount,

        #[Assert\NotNull]
        #[Assert\NotBlank]
        public Currency $currency,

        #[Assert\NotNull]
        #[Assert\NotBlank]
        #[Assert\Choice(callback: [Payment::class, 'getMethodAsChoices'])]
        public readonly string $method,

        public readonly ?string $provider = null,

        public readonly ?string $transactionRef = null
    ) {
    }
}
