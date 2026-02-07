<?php

namespace App\Dto;

use App\Entity\Product;
use Symfony\Component\Validator\Constraints as Assert;

class CreateOptionGroupDto
{
    public function __construct(
        #[Assert\NotNull]
        public ?Product $product = null,

        #[Assert\Length(max: 120)]
        public ?string $label = null,

        public ?bool $isRequired = null,

        public ?int $maxChoices = null,

        public ?bool $isAvailable = null,

        #[Assert\Valid()]
        /** @var array<\App\Entity\OptionItem> */
        public array $optionItems = [],
    ) {
    }
}
