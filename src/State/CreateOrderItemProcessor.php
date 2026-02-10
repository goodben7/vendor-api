<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\CreateOrderItemDto;
use App\Manager\OrderItemManager;
use App\Model\NewOrderItemModel;


class CreateOrderItemProcessor implements ProcessorInterface
{
    public function __construct(
        private OrderItemManager $orderItemManager,
    ) {
    }

    /**
     * @param CreateOrderItemDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {


        $model = new NewOrderItemModel(
            $data->order,
            $data->product,
            $data->quantity,
            $data->orderItemOptions
        );

        return $this->orderItemManager->createOrderItem($model);    
    }
}
