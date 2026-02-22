<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Menu;

class NewCategoryModel
{
    public function __construct(
        #[Assert\NotNull]
        public ?Menu $menu = null,

        #[Assert\Length(max: 120)]
        public ?string $label = null,

        public ?string $description = null,

        public ?int $position = null,
        
        public ?bool $active = null,
    ) {
    }
}
