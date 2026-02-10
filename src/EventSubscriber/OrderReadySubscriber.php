<?php

namespace App\EventSubscriber;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Event\ActivityEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderReadySubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ActivityEvent::getEventName(Order::class, ActivityEvent::ACTION_READY) => 'onOrderReady',
        ];
    }

    public function onOrderReady(ActivityEvent $event): void
    {
        $order = $event->getRessource();
        if (!$order instanceof Order) {
            return;
        }

        foreach ($order->getOrderItems() as $orderItem) {
            if (!$orderItem instanceof OrderItem) {
                continue;
            }
            $orderItem->setItemStatus(OrderItem::STATUS_READY);
            $orderItem->setReadyAt(new \DateTimeImmutable());
        }

        $this->em->flush();
    }
}
