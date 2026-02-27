<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class NewCurrencyModel
{
    public function __construct(
        #[Assert\Currency()]
        #[Assert\NotNull]
        public ?string $code = null,

        public ?string $label = null,

        public ?string $symbol = null,

        public ?bool $active = null,
        
        public ?bool $isDefault = null,
    ) {
    }
}
