<?php

namespace App\State;

use App\Manager\OrderManager;
use App\Dto\CancelOrderDto;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

class CancelOrderProcessor implements ProcessorInterface
{
    public function __construct(private OrderManager $manager)
    {
    }

    /** @param CancelOrderDto $data */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        return $this->manager->cancelOrder($data->order, $data->reason); 
    }
}
