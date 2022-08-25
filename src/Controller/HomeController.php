<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\ForumRepository;
use App\Repository\TopicRepository;
use App\Service\DiscordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Bridge\Discord\DiscordTransport;
use Symfony\Component\Notifier\Bridge\Discord\DiscordTransportFactory;
use Symfony\Component\Notifier\NotifierInterface;
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
        return $this->render('home/index.html.twig', [
            'forums' => $forums,
        ]);
    }
}
