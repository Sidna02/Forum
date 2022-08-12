<?php

namespace App\Twig;

use App\Entity\AbstractPost;
use App\Entity\Category;
use App\Entity\Topic;
use App\Repository\CategoryRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\NonUniqueResultException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CategoryStats extends AbstractExtension
{


    private TopicRepository $topicRepository;
    private CategoryRepository $categoryRepository;


    public function __construct(
        TopicRepository $topicRepository,
        CategoryRepository $categoryRepository
    ) {


        $this->topicRepository = $topicRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('fetchCategoryCount', [$this, 'fetchCount']),
        ];
    }


    public function fetchCount(Category $object, string $statType): int
    {
        if ($statType == "topic") {
            return $this->categoryRepository->countTopicsByCategory($object);
        }
        if ($statType == "comment") {
            return $this->categoryRepository->countCommentsByCategory($object);
        }
    }
}
