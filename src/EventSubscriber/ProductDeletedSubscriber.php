<?php
namespace App\EventSubscriber;

use App\Entity\Product;
use App\Entity\OptionGroup;
use App\Event\ActivityEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductDeletedSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ActivityEvent::getEventName(Product::class, Product::EVENT_DELETED) => 'onProductDeleted',
        ];
    }

    public function onProductDeleted(ActivityEvent $event): void
    {
        $resource = $event->getRessource();
        if (!$resource instanceof Product) {
            return;
        }

        $groups = $resource->getOptionGroups();
        foreach ($groups as $group) {
            if ($group instanceof OptionGroup) {
                $groups->removeElement($group);
                $this->em->remove($group);
            }
        }
        $this->em->flush();
    }
}
