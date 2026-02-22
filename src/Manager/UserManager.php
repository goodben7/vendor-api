<?php

namespace App\Manager;

use App\Entity\User;
use App\Entity\Profile;
use App\Model\NewUserModel;
use App\Model\UpdateUserModel;
use App\Model\UserProxyIntertace;
use App\Manager\PermissionManager;
use App\Model\NewAdminAccessModel;
use App\Repository\UserRepository;
use App\Service\ActivityEventDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;
use App\Exception\InvalidActionInputException;
use App\Exception\UnauthorizedActionException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
        private ActivityEventDispatcher $eventDispatcher,
        private UserRepository $repository,
    ) {  
    }

    public function createFrom(NewUserModel $model): User {

        $user = new User();

        $user->setEmail($model->email);
        $user->setCreatedAt(new \DateTimeImmutable('now'));
        $user->setPlainPassword($model->plainPassword);
        $user->setPassword($this->hasher->hashPassword($user, $model->plainPassword));
        $user->setPhone($model->phone);
        $user->setDisplayName($model->displayName);
        $user->setProfile($model->profile);
        $user->setPersonType($model->profile->getPersonType());
        $user->setHolderId($model->holderId);
        $user->setHolderType($model->holderType);

        $this->em->persist($user);
        $this->em->flush();

        $this->eventDispatcher->dispatch($user, User::EVENT_USER_CREATED);

        return $user;
    }

    public function create(User $user): User  
    {

        if ($user->getPlainPassword()) {
            $user->setPassword($this->hasher->hashPassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();
        }

        $user->setCreatedAt(new \DateTimeImmutable('now'));

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
    
    public function updateFrom(string $userId, UpdateUserModel $model): User {
        
        $user = $this->findUser($userId);

        $user->setEmail($model->email);
        $user->setPhone($model->phone);
        $user->setDisplayName($model->displayName);
        $user->setUpdatedAt(new \DateTimeImmutable('now'));
        
        $this->em->flush();

        $this->eventDispatcher->dispatch($user, User::EVENT_USER_UPDATED);
        
        return $user;
    }

    private function findUser(string $userId): User 
    {
        $user = $this->em->find(User::class, $userId);

        if (null === $user) {
            throw new UnavailableDataException(sprintf('cannot find user with id: %s', $userId));
        }

        return $user; 
    }

    public function changePassword(string $userId, string $actualPassword, string $newPassword): User 
    {
        $user = $this->findUser($userId);


        if (!$this->hasher->isPasswordValid($user, $actualPassword)) {
            throw new InvalidActionInputException('the submitted actual password is not correct');
        }

        $user->setPassword($this->hasher->hashPassword($user, $newPassword));
        $user->setUpdatedAt(new \DateTimeImmutable('now'));
        $user->setMustChangePassword(true);

        $this->em->persist($user);
        $this->em->flush();

        $this->eventDispatcher->dispatch($user, User::EVENT_USER_CHANGED_PASSWORD);

        return $user;
    }

    public function delete(string $userId): void {
        $user = $this->findUser($userId);

        if ($user->isDeleted()) {
            throw new UnauthorizedActionException('this action is not allowed');
        }

        $user->setDeleted(true);
        $user->setUpdatedAt(new \DateTimeImmutable('now'));

        $this->em->persist($user);
        $this->em->flush();

        $this->eventDispatcher->dispatch($user, User::EVENT_USER_DELETED);
    }

    public function lockOrUnlockUser(string|User $user): User
    {
        if (is_string($user)) {
            $user = $this->findUser($user);
        }

        $locked = $user->isLocked();
        $user->setLocked(!$locked);

        $this->em->persist($user);
        $this->em->flush();

        $this->eventDispatcher->dispatch($user, $locked ? User::EVENT_USER_UNLOCKED : User::EVENT_USER_LOCKED);

        return $user;
    }

    /**
     * Assigns a profile to a user or removes it.
     * 
     * This method handles the assignment of a profile to a user, ensuring compatibility
     * between the user's person type and the profile's person type. If the profile is null,
     * the user's profile will be removed.
     *
     * @param string|User $user The user ID or User entity
     * @param string|Profile|null $profile The profile ID, Profile entity, or null to remove profile
     * @return User The updated user entity
     * @throws UnavailableDataException If the user cannot be found
     * @throws InvalidActionInputException If the profile cannot be found or is incompatible
     */
    public function setUserProfile(string|User $user, null|string|Profile $profile = null): User 
    {

        if (is_string($user)) {
            $user = $this->findUser($user);
        }

        if (null === $profile) {
            $user->setProfile(null);
        } else {
            if (is_string($profile)) {
                /** @var Profile|null */
                $profile = $this->em->find(Profile::class, $profile);
                if (null === $profile) {
                    throw new InvalidActionInputException(sprintf('Cannot find profile with ID: %s', $profile));
                }
            }
            
            if ($user->getPersonType() === null) {
                $user->setPersonType($profile->getPersonType());
            } elseif ($user->getPersonType() !== $profile->getPersonType()) {
                throw new InvalidActionInputException('Invalid profile: Person type mismatch between user and profile');
            }

            $user->setProfile($profile);
        }

        $user->setUpdatedAt(new \DateTimeImmutable('now'));
        $this->em->persist($user);
        $this->em->flush();

        $this->eventDispatcher->dispatch($user, User::EVENT_USER_SET_PROFILE);
        
        return $user;
    }
    
    public function addSideRoles(string|User $user, array $sideRoles): User
    {
        if (\is_string($user)) {
            $user = $this->findUser($user);
        }

        $allowed = array_values((array)PermissionManager::getInstance()->getPermissionsAsListChoices());
        
        $valid = array_values(array_unique(array_filter($sideRoles, function ($r) use ($allowed) {
            return \is_string($r) && str_starts_with($r, 'ROLE_') && \in_array($r, $allowed, true);
        })));

        $current = $user->getSideRoles();
        $user->setSideRoles(array_values(array_unique([...$current, ...$valid])));
        $user->setUpdatedAt(new \DateTimeImmutable('now'));

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function createAdminAccess(NewAdminAccessModel $model): User {

        if ($this->repository->findAdminByPlatformId($model->platformId) !== null) {
            throw new InvalidActionInputException('Admin account already exists for this platform');
        }

        if ($model->profile->getPersonType() !== UserProxyIntertace::PERSON_ADMIN) {
            throw new InvalidActionInputException('Invalid profile: Person type must be ADMIN');
        }

        $user = new User();

        $user->setEmail($model->email);
        $user->setCreatedAt(new \DateTimeImmutable('now'));
        $user->setPlainPassword($model->plainPassword);
        $user->setPassword($this->hasher->hashPassword($user, $model->plainPassword));
        $user->setPhone($model->phone);
        $user->setDisplayName($model->displayName);
        $user->setProfile($model->profile);
        $user->setPlatformId($model->platformId);
        $user->setPersonType($model->profile->getPersonType());
        $user->setHolderId($model->holderId);
        $user->setHolderType($model->holderType);
        $user->setAdminAccountCreated(true);

        $this->em->persist($user);
        $this->em->flush();

        $this->eventDispatcher->dispatch($user, User::EVENT_USER_ADMIN_ACCESS_CREATED);

        return $user;
    }
}
