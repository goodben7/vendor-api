<?php
namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\Platform;
use App\Event\ActivityEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserAdminAccessCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ActivityEvent::getEventName(User::class, User::EVENT_USER_ADMIN_ACCESS_CREATED) => 'onAdminAccessCreated',
        ];
    }

    public function onAdminAccessCreated(ActivityEvent $event): void
    {
        $resource = $event->getRessource();
        if (!$resource instanceof User) {
            return;
        }

        $platformId = $resource->getPlatformId();
        if (!$platformId) {
            return;
        }

        /** @var Platform|null $platform */
        $platform = $this->em->find(Platform::class, $platformId);
        if (null === $platform) {
            return;
        }

        if ($platform->getAdminAccountCreated() === true) {
            return;
        }

        $platform->setAdminAccountCreated(true);
        $platform->setUpdatedAt(new \DateTimeImmutable('now'));
        $this->em->flush();
    }
}
