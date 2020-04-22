<?php

namespace App\Repository;

use App\Entity\CGV;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CGV|null find($id, $lockMode = null, $lockVersion = null)
 * @method CGV|null findOneBy(array $criteria, array $orderBy = null)
 * @method CGV[]    findAll()
 * @method CGV[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CGVRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CGV::class);
    }

    // /**
    //  * @return CGV[] Returns an array of CGV objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CGV
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
