<?php

namespace App\Repository;

use App\Entity\OrderItemOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderItemOption>
 *
 * @method OrderItemOption|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderItemOption|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderItemOption[]    findAll()
 * @method OrderItemOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderItemOptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItemOption::class);
    }
}
