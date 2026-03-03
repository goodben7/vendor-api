<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;

class PreviewOrderConversionView
{
    public function __construct(
        #[Groups(['order:get'])]
        public string $orderId,
        #[Groups(['order:get'])]
        public string $baseCurrency,
        #[Groups(['order:get'])]
        public string $targetCurrency,
        #[Groups(['order:get'])]
        public string $baseAmount,
        #[Groups(['order:get'])]
        public string $targetAmount,
        #[Groups(['order:get'])]
        public string $rate,
    ) {
    }
}

