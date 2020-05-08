<?php

namespace App\Repository;

use App\Entity\Blog;
use App\Pagination\Paginator;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Blog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blog[]    findAll()
 * @method Blog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    public function findAllPaginatedAdmin(int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('b')
            ->orderBy('b.createdAt', 'DESC')
        ;
        return (new Paginator($qb))->paginate($page);
    }

    public function findAllPaginated(int $page = 1): Paginator
    {
        try {
            $qb = $this->createQueryBuilder('b')
                ->addSelect('u', 't')
                ->innerJoin('b.user', 'u')
                ->leftJoin('b.tags', 't')
                ->where('b.createdAt <= :now')
                ->orderBy('b.createdAt', 'DESC')
                ->setParameter('now', new DateTime('now', new DateTimeZone('Europe/Paris')));
        } catch (Exception $e) {
        }

        return (new Paginator($qb))->paginate($page);
    }

    public function findAllOrderByView()
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.view', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
            ;
    }
    public function findTotalViews()
    {
        return $this->createQueryBuilder('b')
            ->select('SUM(b.view) as total')
            ->getQuery()
            ->getResult()
            ;
    }
    // /**
    //  * @return Blog[] Returns an array of Blog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Blog
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
