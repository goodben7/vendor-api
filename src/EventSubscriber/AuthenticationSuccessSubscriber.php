<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\Tablet;
use App\Repository\UserRepository;
use App\Storage\DataStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Enum\EntityType;
use App\Exception\UserAuthenticationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private DataStorage $dataStorage
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => 'onAuthenticationSuccess',
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $identifier = $this->security->getUser()->getUserIdentifier();

        /** @var User|null $user */
        $user = $this->userRepository->findByEmailOrPhone($identifier);


        if ($user->isDeleted()) {
            
            throw new UserAuthenticationException('This user is not active. Please contact support.');
        }

        if ($user->getHolderType() === EntityType::TABLET) {
            $tabletId = $user->getHolderId();
            if (!$tabletId) {
                throw new UserAuthenticationException('Tablet access not linked to any device.');
            }
            $tablet = $this->em->find(Tablet::class, $tabletId);
            if (null === $tablet) {
                throw new UserAuthenticationException('Tablet not found.');
            }
            if ($tablet->getDeleted()) {
                throw new UserAuthenticationException('Tablet is deleted. Contact support.');
            }
            if (!$tablet->isActive()) {
                throw new UserAuthenticationException('Tablet is not active. Please enable the device.');
            }
            if ($tablet->getUserId() !== $user->getId()) {
                throw new UserAuthenticationException('Tablet access not linked to this user.');
            }
            if ($tablet->getDeviceId() !== $user->getPhone()) {
                throw new UserAuthenticationException('Tablet device ID does not match.');
            }
        }
    }
}
