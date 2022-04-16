<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/mailer', name: 'app_mailer')]
    public function index(MailerInterface $mailer): Response
    {
        $mailer->
        return $this->render('mailer/index.html.twig', [
            'controller_name' => 'MailerController',
        ]);
    }
}
