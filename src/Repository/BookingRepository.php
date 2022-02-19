<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) 
    {
        parent::__construct($registry, Booking::class);
    }

    public function findBookingWithoutOrdersByUSer(int $id): array
    {
        $querybuilder = $this->createQueryBuilder('b');
        $querybuilder            
            ->innerJoin('b.user', 'u')
            ->leftJoin('b.bookingOrder', 'o')
            ->where('o is NULL')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->orderBy('b.bookingDate', 'ASC')
        ;

        return $querybuilder->getQuery()->getResult();            
    }

    public function findBookingWithOrdersByUSer(int $id)
    {
        $querybuilder = $this->createQueryBuilder('b');
        $querybuilder            
            ->innerJoin('b.user', 'u')
            ->leftJoin('b.bookingOrder', 'o')
            ->where('u.id = :id')
            ->andWhere('o is NOT NULL')
            ->setParameter('id', $id)
            ->orderBy('b.bookingDate', 'ASC')
        ;

        return $querybuilder->getQuery()->getResult();
    }
}
