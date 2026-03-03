<?php

namespace App\Dto;

use App\Entity\Order;
use App\Entity\Currency;
use Symfony\Component\Validator\Constraints as Assert;

class PreviewOrderConversionDto
{
    public function __construct(
        #[Assert\NotNull]
        public ?Order $order = null,

        #[Assert\NotNull]
        public ?Currency $paidCurrency = null,
    ) {
    }
}

