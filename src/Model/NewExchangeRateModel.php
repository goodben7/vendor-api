<?php

namespace App\Model;

use App\Entity\Currency;
use Symfony\Component\Validator\Constraints as Assert;

class NewExchangeRateModel
{
    public function __construct(
        #[Assert\NotNull]
        public ?Currency $baseCurrency = null,

        #[Assert\NotNull]
        public ?Currency $targetCurrency = null,

        #[Assert\NotNull]
        public ?string $baseRate = null,

        #[Assert\NotNull]
        public ?string $targetRate = null,

        public ?bool $active = null,
    ) {
    }
}
