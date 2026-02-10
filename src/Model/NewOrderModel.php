<?php

namespace App\Model;

use App\Entity\Tablet;
use App\Entity\PlatformTable;
use Symfony\Component\Validator\Constraints as Assert;

class NewOrderModel
{
    public function __construct(
        #[Assert\NotBlank]
        public PlatformTable $platformTable,

        #[Assert\NotBlank]
        public Tablet $tablet,

        #[Assert\NotBlank]
        #[Assert\Valid]
        /** @var array<\App\Entity\OrderItem> */
        public array $orderItems
    )
    {
    }
}
