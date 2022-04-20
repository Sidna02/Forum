<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ForumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $forumRepository;

    public function __construct(ForumRepository $forumRepository)
    {
        $this->forumRepository = $forumRepository;

        
    }
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {

 

        

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'forums' => $this->forumRepository->findALl(),
               ]);
    }
}
