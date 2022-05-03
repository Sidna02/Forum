<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    private UserRepository $userRepository;
    private TopicRepository $topicRepository;
    private EntityManager $em;
    private CommentRepository $commentRepository;
    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $em,
        TopicRepository $topicRepository,
        CommentRepository $commentRepository
    ) {
        $this->userRepository = $userRepository;
        $this->topicRepository = $topicRepository;
        $this->commentRepository = $commentRepository;
        $this->em = $em;
    }
    #[Route('/stats', name: 'app_stats')]
    public function index(): Response
    {
        dump("Users count!");
        dump(count($this->userRepository->findAll()));
        dump("Topics count!");
        dump(count($this->topicRepository->findAll()));
        dump("Posts count!");
        dump(count($this->commentRepository->findAll()));
        dump("Latest member");
        dump($this->userRepository->findLatestMember());
        dump($this->userRepository->countRegistrationsByDay());



        return $this->render('base.html.twig', [
            'controller_name' => 'StatsController',
        ]);
    }
}
