<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Topic;
use App\Form\PostCreateType;
use App\Form\TopicCreateType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
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
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Config\BabdevPagerfantaConfig;

class TopicController extends AbstractController
{
    private EntityManager $em;
    private TopicRepository $topicRepository;
    private CommentRepository $commentRepository;
    private CategoryRepository $categoryRepository;
    private ImageRepository $imageRepository;
    public function __construct(
        EntityManagerInterface $em,
        TopicRepository $topicRepository,
        CategoryRepository $categoryRepository,
        CommentRepository $commentRepository,
        ImageRepository $imageRepository

    ) {
        $this->em = $em;
        $this->topicRepository = $topicRepository;
        $this->commentRepository = $commentRepository;
        $this->categoryRepository = $categoryRepository;
        $this->imageRepository = $imageRepository;
    }
    #[Route('/topic/create', name: 'app_topic_create')]
    public function create(Request $request): Response
    {

        $this->denyAccessUnlessGranted("ROLE_USER");
        $category = $this->categoryRepository->findOneBy(['id' => $_GET['id']]);
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
    #[Route('/category/{id}', defaults: ['_format' => 'html'], name: 'app_topic_list')]
    public function listTopics(Request $request, $id, int $page = 1,): Response
    {
        $queryBuilder = $this->topicRepository->getTopicsOrderedByActivity($id);
        $pager = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pager->setMaxPerPage($this->getParameter('pagination')['app.comment.pages'])
            ->setCurrentPage($request->get('page', 1));

            $this->getLastCommentByTopics(iterator_to_array($pager->getCurrentPageResults()));
            return $this->render('topic/list.html.twig.', [
            'topics' => $pager,
            'id' => $id
        ]);
    }
    #[Route('/topic/view/{id}', name: 'app_topic_view')]

    public function viewTopic(Request $request, $id, int $page = 1): Response
    {
        
        $topic = $this->topicRepository->findOneBy(['id' => $id]);
        $queryBuilder = $this->commentRepository->getCommentsOrderedByActivity($id);
        $pager = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pager->setMaxPerPage($this->getParameter('pagination')['app.comment.pages'])->setCurrentPage($request->get('page', 1));

        $images = TopicController::fetchUsersFromComments($pager);
        $images[] = $topic->getAuthor();
        dump($this->imageRepository->fetchUsersProfileImage($images));
        return $this->render('topic/view_topic.html.twig.', [
            'comments' => $pager,
            'profilepictures'=>$this->imageRepository->fetchUsersProfileImage($images, $this->getParameter('default')['userimage']),
            'topic' => $topic,
            'defaultImagePath'=>        '/'.$this->getParameter('defaults_directory') . $this->getParameter('default')['userimage']

        ]);
    }


    #[Route('/topic/view/{id}/create', name: 'app_post_create')]
    public function postCreate(Request $request, $id): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        $topic = $this->topicRepository->findOneBy(['id' => $id]);
        $author = $this->getUser();
        $post =  new Comment($author, $topic);
        $form = $this->createForm(PostCreateType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($post);
            $this->em->flush();
            $this->addFlash("success", "You have successfully created a comment!");
            return $this->redirect($this->generateUrl('app_topic_view', ['id' => $id, 'page' => 1]));
        }

        return $this->render('topic/create_comment.html.twig', [
            'form' => $form->createView()
        ]);
    }
    

    public static function fetchUsersFromComments($comments): array
    {
        $authors = [];
        /***
         * @var Comment[] $comments
         * 
         */
        foreach($comments as $comment)
        {
            if(!empty($comment))
            {
                $authors[] = $comment->getAuthor();

            }
        }
        return $authors;

    }
    public function getLastCommentByTopics(array $topics): array
    {
        //TODO comments or topics
        $lastComments = [];
        foreach($topics as $topic)
        {
            dump($topic);
            $comments = $topic->getComments()->getValues();
            dump($comments);
            
            $count = $topic->getComments()->count();
            dump($count);
            if($count > 0)
            {
                $lastComments[$topic->getId()]  = $comments[0]; 
            }
            else{
                $lastComments[$topic->getId()] = null;
            }       
        }
        dump($lastComments);
        return $lastComments;
 
        
    }
    
}
