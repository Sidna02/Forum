<?php

namespace App\Controller\Admin\Crud;


use App\Entity\Forum;
use App\Form\ForumType;
use App\Repository\ForumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/crud/forum')]
class ForumCrudController extends AbstractController
{
    #[Route('/', name: 'app_forum_crud_index', methods: ['GET'])]
    public function index(ForumRepository $forumRepository): Response
    {
        return $this->render('forum_crud/index.html.twig', [
            'forums' => $forumRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_forum_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ForumRepository $forumRepository): Response
    {
        $forum = new Forum();
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forumRepository->add($forum);
            return $this->redirectToRoute('app_forum_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('forum_crud/new.html.twig', [
            'forum' => $forum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_forum_crud_show', methods: ['GET'])]
    public function show(Forum $forum): Response
    {
        return $this->render('forum_crud/show.html.twig', [
            'forum' => $forum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_forum_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Forum $forum, ForumRepository $forumRepository): Response
    {
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forumRepository->add($forum);
            return $this->redirectToRoute('app_forum_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('forum_crud/edit.html.twig', [
            'forum' => $forum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_forum_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Forum $forum, ForumRepository $forumRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$forum->getId(), $request->request->get('_token'))) {
            $forumRepository->remove($forum);
        }

        return $this->redirectToRoute('app_forum_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
