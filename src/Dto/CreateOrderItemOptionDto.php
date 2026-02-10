<?php

namespace App\Dto;

use App\Entity\OrderItem;
use App\Entity\OptionItem;
use Symfony\Component\Validator\Constraints as Assert;

class CreateOrderItemOptionDto
{
    public function __construct(
        #[Assert\NotBlank]
        public OrderItem $orderItem,

        #[Assert\NotBlank]
        public OptionItem $optionItem,
    )
    {
    }
   
}
