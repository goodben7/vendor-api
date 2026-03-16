<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\Tablet;
use App\Enum\EntityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TabletHeartbeatSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -20],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $principal = $this->security->getUser();
        if (!$principal instanceof User) {
            return;
        }

        if ($principal->getHolderType() !== EntityType::TABLET) {
            return;
        }

        $tabletId = $principal->getHolderId();
        if (!$tabletId) {
            return;
        }

        $tablet = $this->em->find(Tablet::class, $tabletId);
        if (null === $tablet) {
            return;
        }

        $now = new \DateTimeImmutable('now');
        $last = $tablet->getLastHeartbeat();
        if ($last instanceof \DateTimeImmutable) {
            if (($now->getTimestamp() - $last->getTimestamp()) < 15) {
                return;
            }
        }

        $tablet->setLastHeartbeat($now);
        $tablet->setUpdatedAt($now);
        $tablet->setStatus(Tablet::STATUS_ONLINE);
        $this->em->flush();
    }
}
