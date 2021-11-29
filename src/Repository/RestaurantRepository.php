<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Restaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Restaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method array    findAll()
 * @method array    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class RestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }

    // /**
    //  * @return Restaurant[] Returns an array of Restaurant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Restaurant
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return array<Restaurant>
     */
    public function findDistinctCities(mixed $query, int $limit): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.city')->distinct()
            ->andWhere('r.city LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Restaurant>
     */
    public function findAllRestaurantsByName(mixed $query, int $limit): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.name, r.city')
            ->andWhere('r.name LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Restaurant>
     */
    public function findDistinctSpecialities(mixed $query, int $limit): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.speciality')->distinct()
            ->andWhere('r.speciality LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Restaurant>
     */
    public function findBySpecialityAndCity(array $query): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.speciality = :speciality')
            ->andWhere('r.city = :city')
            ->setParameter('speciality', $query['speciality'])
            ->setParameter('city', $query['city'])
            ->getQuery()
            ->getResult();
    }
}
