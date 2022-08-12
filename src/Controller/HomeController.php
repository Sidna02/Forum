<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\ForumRepository;
use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private ForumRepository $forumRepository;
    private TopicRepository $topicRepository;


    public function __construct(
        TopicRepository $topicRepository,
        ForumRepository $forumRepository,
    ) {

        $this->topicRepository = $topicRepository;
        $this->forumRepository = $forumRepository;
    }
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        $forums = $this->forumRepository->findAll();

        //TODO count topics and comments

        return $this->render('home/index.html.twig', [
            'forums' => $forums,
        ]);
    }
}
