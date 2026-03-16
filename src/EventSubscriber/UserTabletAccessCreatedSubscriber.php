<?php
namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\Tablet;
use App\Enum\EntityType;
use App\Event\ActivityEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserTabletAccessCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ActivityEvent::getEventName(User::class, User::EVENT_USER_TABLET_ACCESS_CREATED) => 'onTabletAccessCreated',
        ];
    }

    public function onTabletAccessCreated(ActivityEvent $event): void
    {
        $resource = $event->getRessource();
        if (!$resource instanceof User) {
            return;
        }

        if ($resource->getHolderType() !== EntityType::TABLET) {
            return;
        }

        $tabletId = $resource->getHolderId();
        if (!$tabletId) {
            return;
        }

        /** @var Tablet|null $tablet */
        $tablet = $this->em->find(Tablet::class, $tabletId);
        if (null === $tablet) {
            return;
        }

        if ($tablet->getTabletAccountCreated() === true) {
            return;
        }

        $tablet->setTabletAccountCreated(true);
        $tablet->setUserId($resource->getId());
        $tablet->setUpdatedAt(new \DateTimeImmutable('now'));
        $this->em->flush();
    }
}
