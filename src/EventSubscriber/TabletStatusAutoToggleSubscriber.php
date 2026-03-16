<?php

namespace App\EventSubscriber;

use App\Entity\Tablet;
use App\Storage\DataStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TabletStatusAutoToggleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private DataStorage $dataStorage,
        private int $thresholdSeconds = 60
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -30],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $platformId = $this->dataStorage->getPlatformId();
        if (!$platformId) {
            return;
        }

        $cutoff = (new \DateTimeImmutable())->modify(sprintf('-%d seconds', $this->thresholdSeconds));
        $repo = $this->em->getRepository(Tablet::class);

        $qb = $repo->createQueryBuilder('t')
            ->where('t.platformId = :pid')
            ->andWhere('t.status = :online')
            ->andWhere('t.deleted = false')
            ->andWhere('t.lastHeartbeat IS NULL OR t.lastHeartbeat < :cutoff')
            ->setParameter('pid', $platformId)
            ->setParameter('online', Tablet::STATUS_ONLINE)
            ->setParameter('cutoff', $cutoff);

        $toOffline = $qb->getQuery()->getResult();
        if (!$toOffline) {
            return;
        }

        $now = new \DateTimeImmutable('now');
        foreach ($toOffline as $tablet) {
            $tablet->setStatus(Tablet::STATUS_OFFLINE);
            $tablet->setUpdatedAt($now);
        }
        $this->em->flush();
    }
}

