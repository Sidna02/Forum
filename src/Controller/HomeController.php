<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\ForumRepository;
use App\Repository\TopicRepository;
use App\Entity\Topic;
use App\Entity\Category;
use App\Entity\Forum;
use App\Entity\Image;
use App\Repository\ImageRepository;
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
    private ImageRepository $imageRepository;
    public function __construct(
        EntityManagerInterface $em,
        TopicRepository $topicRepository,
        CategoryRepository $categoryRepository,
        CommentRepository $commentRepository,
        ForumRepository $forumRepository,
        ImageRepository $imageRepository
    ) {
        $this->em = $em;
        $this->topicRepository = $topicRepository;
        $this->commentRepoisotry = $commentRepository;
        $this->categoryRepository = $categoryRepository;
        $this->forumRepository = $forumRepository;
        $this->imageRepository = $imageRepository;
    }
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        $forums = $this->forumRepository->findAll();
        $array = $this->fetchAllCategoriesLastComment($forums);
        $authors = TopicController::fetchUsersFromComments($array);
        $authorsPicture = $this->imageRepository->fetchUsersProfileImage($authors);



        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'forums' => $forums,
            'array' => $array,
            'authorsPicture' => $authorsPicture,
            'defaultImagePath' => $this->getParameter('defaults_directory') . $this->getParameter('default')['userimage'] 
        ]);
    }
    public function fetchAllCategoriesLastComment($forums): array
    {
        $array = [];
        foreach ($forums as $forum) {
            $categories = $forum->getCategories()->getValues();
            foreach ($categories as $category) {
                $res = $this->topicRepository->getLastCommentByCategory($category);
                $array[$category->getId()] = $res;
            }
        }
        return $array;
    }
}
