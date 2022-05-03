<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\ForumRepository;
use App\Repository\TopicRepository;
use App\Entity\Topic;
use App\Entity\Category;
use App\Entity\Forum;
use ArrayIterator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private ForumRepository $forumRepository;
    private EntityManager $em;
    private TopicRepository $topicRepository;
    private CommentRepository $commentRepoisotry;
    private CategoryRepository $categoryRepository;
    public function __construct(
        EntityManagerInterface $em,
        TopicRepository $topicRepository,
        CategoryRepository $categoryRepository,
        CommentRepository $commentRepository,
        ForumRepository $forumRepository
    ) {
        $this->em = $em;
        $this->topicRepository = $topicRepository;
        $this->commentRepoisotry = $commentRepository;
        $this->categoryRepository = $categoryRepository;
        $this->forumRepository = $forumRepository;

    }
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        $array =[];
       $forums = $this->forumRepository->findALl();
       dump($forums);
       foreach($forums as $forum)
       {
         $categories = $forum->getCategories()->getValues();
        foreach($categories as $category)
        {
            $res = $this->topicRepository->getLastCommentByCategory($category);
            $array[$category->getId()] = $res;
        }
  
       }
       dump($array);

       
  

        

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'forums' => $forums,
            'array' => $array,
               ]);
    }
}
