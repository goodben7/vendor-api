<?php

namespace App\State;

use App\Manager\OrderManager;
use App\Dto\SentToKitchenOrderDto;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

class SentToKitchenOrderProcessor implements ProcessorInterface
{
    public function __construct(private OrderManager $manager)
    {
    }

    /** @param SentToKitchenOrderDto $data */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        return $this->manager->markOrderAsSentToKitchen($data->order);
    }
}
