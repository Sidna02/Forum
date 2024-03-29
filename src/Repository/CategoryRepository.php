<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Category $entity, bool $flush = true): void
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
    public function remove(Category $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function countTopicsByCategory(Category $category): int
    {
        $query = $this->_em->createQuery('SELECT count(t) as count FROM App:Topic t WHERE t.category = ?1')
                            ->setParameter(1, $category);
        return $query->getOneOrNullResult()['count'];
    }
    public function countCommentsByCategory(Category $category)
    {
        $query = $this->_em
            ->createQuery("SELECT count(c) as count FROM App:Topic t INNER JOIN App:Comment c WITH c.topic = t.id AND t.category = ?1")
            ->setParameter(1, $category);
        return $query->getOneOrNullResult()['count'];
    }

}
