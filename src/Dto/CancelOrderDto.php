<?php

namespace App\Dto;

use App\Entity\Order;
use Symfony\Component\Validator\Constraints as Assert;

class CancelOrderDto
{
    #[Assert\NotNull]
    #[Assert\NotBlank]
    public Order $order;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    public string $reason;
}
