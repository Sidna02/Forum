<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Form\FileUploadType;
use App\Form\ImageType;
use App\Form\PaginationType;
use App\Repository\ImageRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Yaml\Yaml;

#[IsGranted('ROLE_SUPER_ADMIN')]
class AppSettingsController extends AbstractController
{

    #[Route('/admin/settings/default', name: 'app_settings_index')]
    public function index(Request $request, ImageRepository $imageRepository, FileUploader $fileUploader, AdminUrlGenerator $adminUrlGenerator): Response
    {
        $targetUrl = $adminUrlGenerator
            ->setRoute('app_settings_index')
            ->generateUrl();

        $appDir = $this->getParameter('kernel.project_dir');
        $config = Yaml::parseFile($this->getParameter('config_file'));
        $imageRelativePath = $this->getParameter('defaults_directory') . $this->getParameter('default')['userimage'];
        $image = new File($imageRelativePath);
        $defaultPictureForm = $this->createForm(FileUploadType::class, ['defaultImage' => $image]);
        $defaultPictureForm->handleRequest($request);


        if ($defaultPictureForm->isSubmitted() && $defaultPictureForm->isValid()) {

            $file = $defaultPictureForm->getData()['defaultImage'];
            $config['parameters']['default']['userimage'] = ($imageName = $fileUploader->upload($file, $this->getParameter('defaults_directory')));
            //Writes new default image filename
            file_put_contents($this->getParameter('config_file'), YAML::dump($config, 10));
            unlink($image->getLinkTarget());
            $this->addFlash('success', 'You have successfully changed the default picture');

            return $this->redirect($targetUrl);
        }

        return $this->render('admin/settings/index.html.twig', [
            'defaultImageForm' => $defaultPictureForm->createView(),
            'defaultImagePath' => $imageRelativePath,

        ]);
    }
    #[Route('/admin/settings/pagination', name: 'app_settings_pagination')]
    public function pagination(Request $request)
    {

        $config = Yaml::parseFile($this->getParameter('config_file'));
        $topicPages = &$config['parameters']['pagination']['app.topic.pages'];
        $commentPages = &$config['parameters']['pagination']['app.comment.pages'];
        $form = $this->createForm(PaginationType::class, ['posts_per_page' => &$topicPages, 'comments_per_page' => &$commentPages]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            file_put_contents($this->getParameter('kernel.project_dir') . '/config/appconfig/config.yaml', YAML::dump($config, 10));
        }
        return $this->render('admin/settings/pagination.html.twig', [

            'paginationForm' => $form->createView()
        ]);
    }
}
