<?php

namespace App\Repository;

use App\Entity\User;
use App\Enum\EntityType;
use App\Model\UserProxyIntertace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findByEmailOrPhone(string $emailOrPhone): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :emailOrPhone OR u.phone = :emailOrPhone')
            ->setParameter('emailOrPhone', $emailOrPhone)
            ->getQuery()
            ->getOneOrNullResult()
        ; 
    }

    public function findAdminByPlatformId(string $platformId): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.personType = :personAdmin')
            ->andWhere('u.platformId = :platformId')
            ->setParameter('personAdmin', UserProxyIntertace::PERSON_ADMIN)
            ->setParameter('platformId', $platformId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findTabletByPlatformId(string $platformId, string $holderId): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.platformId = :platformId')
            ->andWhere('u.holderType = :holderType')
            ->andWhere('u.holderId = :holderId')
            ->setParameter('platformId', $platformId)
            ->setParameter('holderType', EntityType::TABLET)
            ->setParameter('holderId', $holderId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
