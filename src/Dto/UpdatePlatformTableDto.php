<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePlatformTableDto
{
    public function __construct(
        #[Assert\Length(max: 120)]
        public ?string $label = null,
        
        public ?int $capacity = null,

        public ?bool $active = null,
    ) {
    }
}
