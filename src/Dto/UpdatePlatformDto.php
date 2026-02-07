<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePlatformDto
{
    public function __construct(
        #[Assert\Length(max: 180)]
        public ?string $name = null,

        public ?string $address = null,
        
        public ?string $description = null,

        #[Assert\Length(max: 3)]
        public ?string $currency = null,

        public ?array $paymentConfigJson = null,
        
        public ?bool $active = null,
    ) {
    }
}
