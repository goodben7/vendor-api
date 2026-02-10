<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\CreateOrderItemOptionDto;
use App\Manager\OrderItemOptionManager;
use App\Model\NewOrderItemOptionModel;
class CreateOrderItemOptionProcessor implements ProcessorInterface
{
    public function __construct(
        private OrderItemOptionManager $orderItemOptionManager,
    ) {
    }

    /**
     * @param CreateOrderItemOptionDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        $model = new NewOrderItemOptionModel(
            $data->orderItem,
            $data->optionItem
        );

        return $this->orderItemOptionManager->createOrderItemOption($model);
    }
}
