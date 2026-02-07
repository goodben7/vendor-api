<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Menu;

class UpdateCategoryDto
{
    public function __construct(

        public ?Menu $menu = null,

        #[Assert\Length(max: 120)]
        public ?string $label = null,

        public ?int $position = null,
        
        public ?bool $active = null,
    ) {
    }
}
