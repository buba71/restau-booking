<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BookingOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class BookingOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingOrder::class);
    }

    public function findAllOrderByBookingDate()
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder->leftJoin('o.booking', 'b')
                     ->orderBy('b.bookingDate', 'asc');

        return $queryBuilder->getQuery()->getResult();
    }
}