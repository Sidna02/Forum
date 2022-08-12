<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Topic;
use App\Form\PostCreateType;
use App\Form\TopicCreateType;
use App\Service\PagerService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TopicController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
    ) {
        $this->em = $em;
    }

    #[Route('/topic/create/{category}', name: 'app_topic_create')]
    public function create(Request $request, Category $category): Response
    {

        $this->denyAccessUnlessGranted("ROLE_USER");
        $creator = $this->getUser();
        $topic = new Topic($creator, $category);

        $form = $this->createForm(TopicCreateType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($topic);
            $this->em->flush();
            $this->addFlash("success", "You have successfully created a topic!");
            return $this->redirectToRoute("app_topic_view", ['id' => $topic->getId()]);
        }

        return $this->render('topic/create_post.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/category/{id}', name: 'app_topic_list', defaults: ['_format' => 'html'])]
    #[ParamConverter('category', Category::class)]
    public function listTopics(Category $category, PagerService $pagerService): Response
    {
        $pager = $pagerService->getPagerForTopics($category);

        return $this->render('topic/list.html.twig.', [
            'topics' => $pager,
            'currentCategory' => $category,

        ]);
    }


    #[Route('/topic/view/{id}', name: 'app_topic_view')]
    public function viewTopic(Topic $topic, PagerService $pagerService, int $currentPage = 1): Response
    {
        $pager = $pagerService->getPagerForComments($topic, $currentPage);


        return $this->render('topic/view_topic.html.twig.', [
            'comments' => $pager,
            'topic' => $topic,
        ]);
    }


    #[Route('/topic/view/{id}/create', name: 'app_post_create')]
    public function commentCreate(Request $request, Topic $topic): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        $author = $this->getUser();
        $post = new Comment($author, $topic);
        $form = $this->createForm(PostCreateType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $topic->addComment($post);
            $this->em->flush();
            $this->addFlash("success", "You have successfully created a comment in " .$topic->getTitle());
            return $this->redirect($this->generateUrl('app_topic_view', ['id' => $topic->getId(), 'page' => 1]));
        }

        return $this->render('topic/create_comment.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
