<?php

namespace App\Manager;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Event\ActivityEvent;
use App\Model\NewOrderModel;
use App\Entity\OrderItemOption;
use App\Service\ActivityEventDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\InvalidActionInputException;
use App\Entity\Currency;
use App\Entity\Platform;
use App\Service\CurrencyConverter;

class OrderManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ActivityEventDispatcher $eventDispatcher,
        private CurrencyConverter $currencyConverter,
    ) {
    }

    public function createOrder(NewOrderModel $model): Order
    {
        $order = new Order();
        $order->setPlatformTable($model->platformTable);
        $order->setTablet($model->tablet);
        $order->setReferenceUnique($this->generateReferenceUnique());
        $order->setStatus(Order::STATUS_DRAFT);
        $order->setCreatedAt(new \DateTimeImmutable());

        $totalAmount = 0.0;

        foreach ($model->orderItems as $item) {
            $product = $item->getProduct();

            if (!$product->isAvailable()) {
                throw new \DomainException(
                    \sprintf('Le produit "%s" est indisponible.', $product->getLabel())
                );
            }

            $orderItem = new OrderItem();
            $orderItem->setOrder($order);
            $orderItem->setProduct($product);
            $orderItem->setQuantity($item->getQuantity());
            $orderItem->setItemStatus(OrderItem::STATUS_PENDING);

            $unitPrice = (float) $product->getBasePrice();
            $orderItem->setUnitPriceOrder($unitPrice);

            $itemTotal = $unitPrice * $orderItem->getQuantity();

            foreach ($item->getOrderItemOptions() as $itemOption) {
                $optionItem = $itemOption->getOptionItem();

                $orderItemOption = new OrderItemOption();
                $orderItemOption->setOrderItem($orderItem);
                $orderItemOption->setOptionItem($optionItem);

                $optionPrice = (float) $optionItem->getPriceDelta();
                $orderItemOption->setPriceSnapshot($optionPrice);

                $itemTotal += $optionPrice * $orderItem->getQuantity();

                $orderItem->addOrderItemOption($orderItemOption);
            }

            $totalAmount += $itemTotal;
            $order->addOrderItem($orderItem);
        }

        $order->setTotalAmount($totalAmount);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch($order, ActivityEvent::ACTION_CREATE);

        return $order;
    }

    public function previewConversion(Order $order, Currency $paidCurrency): array
    {
        $platformId = $order->getPlatformId();
        if (null === $platformId) {
            throw new InvalidActionInputException('Platform not found');
        }
        $platform = $this->entityManager->find(Platform::class, $platformId);
        if (null === $platform) {
            throw new InvalidActionInputException('Platform not found');
        }
        if (null === $platform->getCurrency()) {
            throw new InvalidActionInputException('Platform currency must be set.');
        }
        $restaurantCurrency = $platform->getCurrency();
        $baseAmount = $order->getTotalAmount();
        if ($paidCurrency->getId() === $restaurantCurrency->getId()) {
            $targetAmount = $baseAmount;
            $rate = '1';
        } else {
            $targetAmount = $this->currencyConverter->convert($restaurantCurrency, $paidCurrency, $baseAmount);
            $rate = $this->currencyConverter->getRate($restaurantCurrency, $paidCurrency);
        }
        return [
            'orderId' => $order->getId(),
            'baseCurrency' => $restaurantCurrency->getId(),
            'targetCurrency' => $paidCurrency->getId(),
            'baseAmount' => $baseAmount,
            'targetAmount' => $targetAmount,
            'rate' => $rate,
        ];
    }

    public function markOrderAsSentToKitchen(Order $order): Order
    {
        if ($order->getStatus() !== Order::STATUS_DRAFT) {
            throw new InvalidActionInputException('Action not allowed : invalid order state');
        }

        $order->setStatus(Order::STATUS_SENT_TO_KITCHEN);
        $order->setSentToKitchenAt(new \DateTimeImmutable());
        $order->setUpdatedAt(new \DateTimeImmutable());
        
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch($order, ActivityEvent::ACTION_SENT_TO_KITCHEN);

        return $order; 
    }

    public function markOrderAsReady(Order $order): Order
    {
        if ($order->getStatus() !== Order::STATUS_SENT_TO_KITCHEN) {
            throw new InvalidActionInputException('Action not allowed: Order must be in "SENT_TO_KITCHEN" status.');
        }

        $order->setStatus(Order::STATUS_READY);
        $order->setReadyAt(new \DateTimeImmutable());
        $order->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch($order, ActivityEvent::ACTION_READY);

        return $order;
    }

    public function markOrderAsServed(Order $order): Order
    {
        if ($order->getStatus() !== Order::STATUS_READY) {
            throw new InvalidActionInputException('Action not allowed: Order must be in "READY" status.');
        }

        $order->setStatus(Order::STATUS_SERVED);
        $order->setServedAt(new \DateTimeImmutable());
        $order->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch($order, ActivityEvent::ACTION_SERVED);

        return $order;
    }

    public function cancelOrder(Order $order, string $reason): Order
    {
        if ($order->getStatus() === Order::STATUS_PAID || $order->getStatus() === Order::STATUS_CANCELLED) {
            throw new InvalidActionInputException('Action not allowed: Order is already paid or cancelled.');
        }

        $order->setStatus(Order::STATUS_CANCELLED);
        $order->setCancelledAt(new \DateTimeImmutable());
        $order->setCancellationReason($reason);
        $order->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch($order, ActivityEvent::ACTION_CANCELLED); 

        return $order;
    }

    private function generateReferenceUnique(): string
    {
        $datePart = (new \DateTime())->format('Ymd');
        $randomPart = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        $reference = sprintf('ORD-%s-%s', $datePart, $randomPart);

        while ($this->entityManager->getRepository(Order::class)->findOneBy(['referenceUnique' => $reference])) {
            $randomPart = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            $reference = sprintf('ORD-%s-%s', $datePart, $randomPart);
        }

        return $reference;
    }


}
