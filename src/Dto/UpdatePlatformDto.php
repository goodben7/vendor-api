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

        #[Assert\Length(max: 30)]
        public ?string $phone = null,

        #[Assert\Length(max: 180)]
        #[Assert\Email]
        public ?string $email = null,

        public ?bool $allowTableManagement = null,
        public ?bool $allowOnlineOrder = null,

        public ?array $paymentConfigJson = null,
        
        public ?bool $active = null,
    ) {
    }
}
