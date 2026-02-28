<?php

namespace App\Service;

use App\Entity\Currency;
use App\Repository\ExchangeRateRepository;
use App\Exception\UnavailableDataException;

class CurrencyConverter
{
    public function __construct(
        private ExchangeRateRepository $rates
    ) {
    }

    public function convert(Currency $from, Currency $to, string $amount): string
    {
        if ($from->getId() === $to->getId()) {
            return $this->normalizeAmount($amount);
        }
        $rate = $this->rates->findActiveRate($from, $to);
        if ($rate !== null) {
            return $rate->convert($amount);
        }
        
        throw new UnavailableDataException('Exchange rate not found.');
    }

    public function getRate(Currency $base, Currency $target): string
    {
        if ($base->getId() === $target->getId()) {
            return '1';
        }
        $rate = $this->rates->findActiveRate($base, $target);
        if ($rate !== null) {
            return bcadd($rate->getRate(), '0', 2);
        }

        throw new UnavailableDataException('Active exchange rate not found');
    }

    private function normalizeAmount(string $amount): string
    {
        return $this->round($amount, 2);
    }

    private function round(string $amount, int $precision): string
    {
        return bcadd($amount, '0', $precision);
    }
}
