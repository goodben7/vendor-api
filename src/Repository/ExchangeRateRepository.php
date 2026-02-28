<?php

namespace App\Repository;

use App\Entity\ExchangeRate;
use App\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    public function findActiveRate(Currency $base, Currency $target): ?ExchangeRate
    {
        return $this->createQueryBuilder('er')
            ->andWhere('er.active = :active')
            ->andWhere('er.baseCurrency = :base')
            ->andWhere('er.targetCurrency = :target')
            ->setParameter('active', true)
            ->setParameter('base', $base)
            ->setParameter('target', $target)
            ->orderBy('er.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getRate(Currency $base, Currency $target): ?string
    {
        try {
            return $this->createQueryBuilder('er')
                ->select('er.rate')
                ->andWhere('er.active = :active')
                ->andWhere('er.baseCurrency = :base')
                ->andWhere('er.targetCurrency = :target')
                ->setParameter('active', true)
                ->setParameter('base', $base)
                ->setParameter('target', $target)
                ->orderBy('er.createdAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException|\Doctrine\ORM\NonUniqueResultException) {
            return null;
        }
    }
}
