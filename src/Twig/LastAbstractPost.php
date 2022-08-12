<?php

namespace App\Twig;

use App\Entity\AbstractPost;
use App\Entity\Category;
use App\Entity\Topic;
use App\Repository\TopicRepository;
use Doctrine\ORM\NonUniqueResultException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LastAbstractPost extends AbstractExtension
{


    private TopicRepository $topicRepository;


    public function __construct(
        TopicRepository $topicRepository,
    ) {


        $this->topicRepository = $topicRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('lastAbstractPost', [$this, 'lastAbstractPost']),
        ];
    }

    public function lastAbstractPost(Category | Topic $object): ?AbstractPost
    {
        if ($object instanceof Category) {
            return $this->topicRepository->getLastTopicByCategory($object);
        }
        if ($object instanceof Topic) {
            return $this->topicRepository->getLastAbstractPostByTopic($object);
        }
    }
}
