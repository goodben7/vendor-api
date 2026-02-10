<?php

namespace App\Model;

use App\Entity\OrderItem;
use App\Entity\OptionItem;
use Symfony\Component\Validator\Constraints as Assert;

class NewOrderItemOptionModel
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
