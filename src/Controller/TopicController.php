<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Form\TopicCreateType;
use App\Repository\TopicRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TopicController extends AbstractController
{
    private $em;
    private $topicRepository;
    public function __construct(EntityManagerInterface $em, TopicRepository $topicRepository)
    {
        $this->em = $em;
        $this->topicRepository = $topicRepository;
    }
    #[Route('/topic/create', name: 'app_topic_create')]
    public function create(Request $request): Response
    {
       $this->denyAccessUnlessGranted("ROLE_USER");
       $creator = $this->getUser();
       $topic =  new Topic($creator);
       $form = $this->createForm(TopicCreateType::class, $topic);
       $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) 
        { 

            $topic->setCreatedAt(new DateTimeImmutable());
            $this->em->persist($topic);
            $this->em->flush();
            $this->addFlash("success", "You have successfully created a topic!");
            return $this->redirectToRoute("app_topic_list");
        }

        return $this->render('topic/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/topic/list', name: 'app_topic_list')]

    public function listTopics() : Response
    {

        $topics = $this->topicRepository->findAll();
        return $this->render('topic/list.html.twig.',[
            'topics' => $topics
        ]);
    }
}
