<?php

namespace App\Model;

use App\Entity\Order;
use App\Entity\Product;
use Symfony\Component\Validator\Constraints as Assert;

class NewOrderItemModel
{
    public function __construct(
        #[Assert\NotBlank]
        public Order $order,
        
        #[Assert\NotBlank]
        public Product $product,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public int $quantity,

        #[Assert\Valid]
        /** @var array<\App\Entity\OrderItemOption> */
        public array $orderItemOptions = [],
    )
    {
    }
}
