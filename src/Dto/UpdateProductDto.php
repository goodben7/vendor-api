<?php

namespace App\Dto;

use App\Entity\Category;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateProductDto
{
    public function __construct(
        public ?Category $category = null,

        #[Assert\Length(max: 120)]
        public ?string $label = null,

        #[Assert\Length(max: 255)]
        public ?string $description = null,

        public ?string $basePrice = null,
        
        public ?bool $isAvailable = null,
    ) {
    }
}
