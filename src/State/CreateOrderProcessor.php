<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\CreateOrderDto;
use App\Manager\OrderManager;
use App\Model\NewOrderModel;

class CreateOrderProcessor implements ProcessorInterface
{
    public function __construct(private OrderManager $orderManager)
    {
    }

    /**
     * @param CreateOrderDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new NewOrderModel(
            $data->platformTable,
            $data->tablet,
            $data->orderItems
        );

        return $this->orderManager->createOrder($model);
    }
}
