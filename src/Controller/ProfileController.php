<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Entity\User;
use App\Form\ProfilesettingsType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ProfileController extends AbstractController
{
    private EntityManager $em;
    private TopicRepository $topicRepository;
    private CommentRepository $commentRepository;
    private CategoryRepository $categoryRepository;
    private UserRepository $userRepository;
    private ImageRepository $imageRepository;
    public function __construct(
        EntityManagerInterface $em,
        TopicRepository $topicRepository,
        CategoryRepository $categoryRepository,
        CommentRepository $commentRepository,
        ImageRepository $imageRepository,
        UserRepository $userRepository
    ) {
        $this->em = $em;
        $this->topicRepository = $topicRepository;
        $this->commentRepository = $commentRepository;
        $this->categoryRepository = $categoryRepository;
        $this->imageRepository = $imageRepository;
        $this->userRepository = $userRepository;
    }
    #[Route('/profile/settings', name: 'app_profile_settings')]
    public function index(Request $request): Response
    {
        /***
         * @var User $user
         * 
         */
        $user = $this->getUser();
        $picture = $this->imageRepository->findOneBy(['id' => $user->getProfileImage()]);
        if (!$picture) {
            $picture = new Image();
        }
        $form = $this->createForm(ImageType::class, $picture);
        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->persist($picture);
                $this->em->flush();
                $user->setProfileImage($picture);
                $this->em->flush();
            }

        $profileForm = $this->createForm(ProfilesettingsType::class, $user);

        $profileForm->handleRequest($request);


            if ($profileForm->isSubmitted() && $profileForm->isValid()) {
                $this->em->flush();
            }


        return $this->render('profile/index.html.twig', [
            'profilePictureForm' => $form->createView(),
            'profileSettingsForm' => $profileForm->createView(),
            'image' => $this->em->getRepository('App\Entity\User')->fetchUserImage($user)
        ]);
    }
}
