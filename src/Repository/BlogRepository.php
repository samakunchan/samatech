<?php

namespace App\Repository;

use App\Entity\Blog;
use App\Pagination\Paginator;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use function Symfony\Component\String\u;

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

    public function findAllPaginated($tag, int $page = 1): Paginator
    {
        try {
            $qb = $this->createQueryBuilder('b')
                ->addSelect('u', 't')
                ->innerJoin('b.user', 'u')
                ->leftJoin('b.tags', 't')
                ->where('b.createdAt <= :now')
                ->orderBy('b.createdAt', 'DESC')
                ->andWhere('b.status = 1')
                ->setParameter('now', new DateTime('now', new DateTimeZone('Europe/Paris')));
        } catch (Exception $e) {
        }

        if (null !== $tag) {
            $qb->andWhere(':tag MEMBER OF b.tags')
                ->setParameter('tag', $tag);
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

    /**
     * @param string $query
     * @param int $limit
     * @return Blog[]
     */
    public function findBySearchQuery(string $query, int $limit = Blog::NUM_ITEMS): array
    {
        $searchTerms = $this->extractSearchTerms($query);

        if (0 === \count($searchTerms)) {
            return [];
        }

        $queryBuilder = $this->createQueryBuilder('b');

        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere('b.title LIKE :t_'.$key)
                ->setParameter('t_'.$key, '%'.$term.'%')
            ;
        }

        return $queryBuilder
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Transforms the search string into an array of search terms.
     * @param string $searchQuery
     * @return array
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $searchQuery = u($searchQuery)->replaceMatches('/[[:space:]]+/', ' ')->trim();
        $terms = array_unique(u($searchQuery)->split(' '));

        // ignore the search terms that are too short
        return array_filter($terms, function ($term) {
            return 2 <= u($term)->length();
        });
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
