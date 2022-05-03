<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Topic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Topic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Topic[]    findAll()
 * @method Topic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Topic $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Topic $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Topic[] Returns an array of Topic objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Topic
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getLastCommentByCategory(Category $cat)
    {
        $res= $this->_em->createQuery("SELECT co from App:Category c INNER JOIN App:Topic ti WITH ti.category = ?1 INNER JOIN App:Comment co WITH ti.id=co.topic ORDER BY co.createdAt DESC")
        ->setParameter(1, $cat->getId())
        ->setMaxResults(1)
        ->getOneOrNullResult();
        if($res == null)
        {
            return $this->getLastTopicByCategory($cat);
        }
        return $res;
    }
    public function getLastTopicByCategory(Category $cat)
    {
        $res= $this->_em->createQuery("SELECT ti from App:Category c INNER JOIN App:Topic ti WITH ti.category = ?1 ORDER BY ti.createdAt DESC")
        ->setParameter(1, $cat->getId())
        ->setMaxResults(1)
        ->getOneOrNullResult();
        return $res;
    }
}
