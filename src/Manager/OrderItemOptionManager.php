<?php

namespace App\Manager;

use App\Entity\OrderItemOption;
use App\Event\ActivityEvent;
use App\Model\NewOrderItemOptionModel;
use App\Service\ActivityEventDispatcher;
use Doctrine\ORM\EntityManagerInterface;

class OrderItemOptionManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ActivityEventDispatcher $eventDispatcher,
    ) {
    }

    public function createOrderItemOption(NewOrderItemOptionModel $model): OrderItemOption
    {
        $orderItem = $model->orderItem;
        $optionItem = $model->optionItem; 

        $orderItemOption = new OrderItemOption();
        $orderItemOption->setOrderItem($orderItem);
        $orderItemOption->setOptionItem($optionItem);

        $optionPrice = (float) $optionItem->getPriceDelta();
        $orderItemOption->setPriceSnapshot($optionPrice);

        $orderItem->addOrderItemOption($orderItemOption);

        $order = $orderItem->getOrder();
        $itemQuantity = $orderItem->getQuantity();
        $order->setTotalAmount($order->getTotalAmount() + ($optionPrice * $itemQuantity));

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch($orderItemOption, ActivityEvent::ACTION_CREATE);

        return $orderItemOption;
    }
}
