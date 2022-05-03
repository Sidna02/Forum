<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Topic;
use App\Form\PostCreateType;
use App\Form\TopicCreateType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\TopicRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TopicController extends AbstractController
{
    //TODO Pagination
    private EntityManager $em;
    private TopicRepository $topicRepository;
    private CommentRepository $commentRepoisotry;
    private CategoryRepository $categoryRepository;
    public function __construct(
        EntityManagerInterface $em,
        TopicRepository $topicRepository,
        CategoryRepository $categoryRepository,
        CommentRepository $commentRepository
    ) {
        $this->em = $em;
        $this->topicRepository = $topicRepository;
        $this->commentRepoisotry = $commentRepository;
        $this->categoryRepository = $categoryRepository;
    }
    #[Route('/topic/create', name: 'app_topic_create')]
    public function create(Request $request): Response
    {

        $this->denyAccessUnlessGranted("ROLE_USER");
        $category = $this->categoryRepository->findOneBy(['id'=>$_GET['id']]);
        $creator = $this->getUser();
        $topic =  new Topic($creator, $category);
    
        $form = $this->createForm(TopicCreateType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($topic);
            
            $this->em->flush();
            $this->addFlash("success", "You have successfully created a topic!");
            return $this->redirectToRoute("app_topic_list", ['id' => $_GET['id']]);
        }

        return $this->render('topic/create_post.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/category/{id}', name: 'app_topic_list')]

    public function listTopics($id): Response
    {
        $topics = $this->topicRepository->findBy(['category' => $id], ['createdAt' => 'DESC']);

        return $this->render('topic/list.html.twig.', [
            'topics' => $topics,
            'id'=>$id
        ]);
    }
    #[Route('/topic/view/{id}', name: 'app_topic_view')]

    public function viewTopic($id): Response
    {
        $topic = $this->topicRepository->findOneBy(['id'=>$id]);

        return $this->render('topic/view_topic.html.twig.', [
            'topic' => $topic
        ]);
    }


    #[Route('/topic/view/{id}/create', name: 'app_post_create')]
    public function postCreate(Request $request, $id): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        $topic = $this->topicRepository->findOneBy(['id'=>$id]);
        $author = $this->getUser();
        $post =  new Comment($author, $topic);
        $form = $this->createForm(PostCreateType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($post);
            $this->em->flush();
            $this->addFlash("success", "You have successfully created a comment!");
            return $this->redirect($this->generateUrl('app_topic_view', ['id' => $id]));
        }

        return $this->render('topic/create_comment.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
