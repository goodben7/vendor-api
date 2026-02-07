<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePlatformModel
{
    public function __construct(
        #[Assert\Length(max: 180)]
        public ?string $name = null,

        public ?string $address = null,
        
        public ?string $description = null,

        #[Assert\Length(max: 3)]
        #[Assert\Currency()]
        public ?string $currency = null,

        public ?array $paymentConfigJson = null,

        public ?bool $active = null,
    ) {
    }
}
