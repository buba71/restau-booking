<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ClosedDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ClosedDateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClosedDate::class);
    }

    public function findByRestaurant(int $id) 
    {
        $queryBuilder = $this->createQueryBuilder('c');
        
        $queryBuilder->select('c')
                     ->where('c.restaurant = :id')
                     ->setParameter('id', $id)
        ;

        

        return $queryBuilder->getQuery()->getResult();
        
    }

}