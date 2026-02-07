<?php

namespace App\Model;

use App\Entity\Product;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateOptionGroupModel
{
    public function __construct(
        public ?Product $product = null,
        #[Assert\Length(max: 120)]
        public ?string $label = null,
        public ?bool $isRequired = null,
        public ?int $maxChoices = null,
        public ?bool $isAvailable = null,
    ) {
    }
}
