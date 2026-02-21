<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Storage\DataStorage;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DataStorageSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly DataStorage $dataStorage,
        private readonly Security $security,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        
        $this->dataStorage->clear();
        
        $headers = [];
        foreach ($request->headers->all() as $key => $value) {
            $headers[$key] = $value[0] ?? null;
        }
        $this->dataStorage->setHeaders($headers);
        
        /** @var User|null $user */
        $user = $this->security->getUser();
        
        if ($this->logger) {
            $this->logger->debug('DataStorageSubscriber::onKernelRequest - User authentication check', [
                'is_authenticated' => $user !== null,
                'user_class' => $user ? get_class($user) : 'null',
                'user_id' => $user?->getId(),
                'has_platform_id' => $user?->getPlatformId() ? 'yes' : 'no',
                'platform_id' => $user?->getPlatformId()
            ]);
        }
        
        if ($user?->getPlatformId()) {
            $this->dataStorage->setPlatformId($user->getPlatformId());
            
            if ($this->logger) {
                $this->logger->info('DataStorageSubscriber::onKernelRequest - Platform ID set in DataStorage', [
                    'platform_id' => $user->getPlatformId(),
                    'user_id' => $user->getId(),
                    'storage_class' => \get_class($this->dataStorage)
                ]);
            }
        } else if ($this->logger) {
            $this->logger->info('DataStorageSubscriber::onKernelRequest - No platform ID available to set');
        }
    }
}