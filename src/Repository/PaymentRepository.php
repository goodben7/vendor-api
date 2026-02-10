<?php

namespace App\Repository;

use App\Entity\Payment;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 *
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function findSuccessfulForPaidOrder(Order $order): ?Payment
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.order', 'o')
            ->where('p.order = :order')
            ->andWhere('p.status = :paymentStatus')
            ->andWhere('o.status = :orderStatus')
            ->setParameter('order', $order)
            ->setParameter('paymentStatus', Payment::STATUS_SUCCESS)
            ->setParameter('orderStatus', Order::STATUS_PAID)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
