<?php

namespace App\Repository;

use App\Entity\RemembermeToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RemembermeToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method RemembermeToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method RemembermeToken[]    findAll()
 * @method RemembermeToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RemembermeTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RemembermeToken::class);
    }

    // /**
    //  * @return RemembermeToken[] Returns an array of RemembermeToken objects
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
    public function findOneBySomeField($value): ?RemembermeToken
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
