<?php

namespace App\Manager;

use App\Entity\OrderItem;
use App\Entity\OrderItemOption;
use App\Event\ActivityEvent;
use App\Model\NewOrderItemModel;
use App\Service\ActivityEventDispatcher;
use Doctrine\ORM\EntityManagerInterface;

class OrderItemManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ActivityEventDispatcher $eventDispatcher,
    ) {
    }

    public function createOrderItem(NewOrderItemModel $model): OrderItem
    {
        $product = $model->product;

        if (!$product->isAvailable()) {
            throw new \DomainException(
                \sprintf('Le produit "%s" est indisponible.', $product->getLabel())
            );
        }

        $orderItem = new OrderItem();
        $orderItem->setOrder($model->order);
        $orderItem->setProduct($product);
        $orderItem->setQuantity($model->quantity);
        $orderItem->setItemStatus(OrderItem::STATUS_PENDING);

        $unitPrice = (float) $product->getBasePrice();
        $orderItem->setUnitPriceOrder($unitPrice);

        $itemTotal = $unitPrice * $orderItem->getQuantity();

        foreach ($model->orderItemOptions as $itemOptionModel) {
            $optionItem = $itemOptionModel->getOptionItem();

            $orderItemOption = new OrderItemOption();
            $orderItemOption->setOrderItem($orderItem);
            $orderItemOption->setOptionItem($optionItem);

            $optionPrice = (float) $optionItem->getPriceDelta();
            $orderItemOption->setPriceSnapshot($optionPrice);

            $itemTotal += $optionPrice * $orderItem->getQuantity();

            $orderItem->addOrderItemOption($orderItemOption);
        }

        $model->order->addOrderItem($orderItem);
        $model->order->setTotalAmount($model->order->getTotalAmount() + $itemTotal);

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch($orderItem, ActivityEvent::ACTION_CREATE);

        return $orderItem;
    }
}
