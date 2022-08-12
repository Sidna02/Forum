<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    private UserRepository $userRepository;
    private TopicRepository $topicRepository;
    private CommentRepository $commentRepository;
    public function __construct(
        UserRepository $userRepository,
        TopicRepository $topicRepository,
        CommentRepository $commentRepository
    ) {
        $this->userRepository = $userRepository;
        $this->topicRepository = $topicRepository;
        $this->commentRepository = $commentRepository;
    }
    #[Route('/stats', name: 'app_stats')]
    public function index(): Response
    {
        $stats = [];
        $lastMember = $this->userRepository->findLatestMember();
        $stats['Latest Member'] = $lastMember != null? $lastMember->getUsername() : "None";
        $stats['Comments Count'] = $this->commentRepository->countComments();
        $stats['Topics Count'] = $this->topicRepository->countTopics();
        $stats['Users Count'] = $this->userRepository->countUsers();

        return $this->render('widget_stats.html.twig', [
        'stats'=>$stats,
        ]);
    }
}
