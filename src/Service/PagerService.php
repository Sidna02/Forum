<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Entity\Topic;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;

class PagerService
{

    private EntityManagerInterface $em;
    private ConfigHandler $configHandler;

    public function __construct(
        EntityManagerInterface $em,
        ConfigHandler          $configHandler
    ) {
        $this->em = $em;
        $this->configHandler = $configHandler;
    }

    /**
     * @param Category $category
     * @param int $currentPage
     * @return PagerfantaInterface
     */
    public function getPagerForTopics(Category $category, int $currentPage = 1): PagerfantaInterface
    {
        return (new Pagerfanta(
            $this->getTopicsAdapterOrderedByActivity($category)
        ))
            ->setCurrentPage($currentPage)
            ->setMaxPerPage($this->configHandler->getTopicPagination());
    }

    /**
     * @param Topic $topic
     * @param int $currentPage
     * @return PagerfantaInterface
     */
    public function getPagerForComments(Topic $topic, int $currentPage): PagerfantaInterface
    {
        return (new Pagerfanta($this->getCommentsAdapterOrderedByActivity($topic)))
            ->setCurrentPage($currentPage)
            ->setMaxPerPage($this->configHandler->getCommentPagination());
    }

    /**
     * @param Category $category
     * @return QueryAdapter
     */
    private function getTopicsAdapterOrderedByActivity(Category $category): QueryAdapter
    {
        return new QueryAdapter(
            $this->em->createQueryBuilder()
            ->select(['t'])
            ->from('App:Topic', 't')
            ->orderBy('t.lastActivity', 'DESC')
            ->where('t.category =:cat')
            ->setParameter('cat', $category)
        );
    }


    /**
     * @param Topic $topic
     * @return QueryAdapter
     */
    private function getCommentsAdapterOrderedByActivity(Topic $topic): QueryAdapter
    {

        return new QueryAdapter($this->em->createQueryBuilder()
            ->select(['c'])
            ->from('App:Comment', 'c')
            ->where('c.topic =:topic')
            ->orderBy('c.createdAt', 'ASC')
            ->setParameter('topic', $topic));
    }
}
