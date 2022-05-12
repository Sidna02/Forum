<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Crud\UserCrudController;
use App\Controller\Admin\Stats\AdminStats;
use App\Entity\Category;
use App\Entity\Forum;
use App\Entity\Group;
use App\Entity\User;
use App\Repository\ImageRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SubMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[IsGranted("ROLE_ADMIN")]

class AdminDashboardController extends AbstractDashboardController
{
    private AdminStats $adminStats;
    private ImageRepository $imageRepository;
    public function __construct(AdminStats $adminStats, ImageRepository $imageRepository)
    {
        $this->adminStats = $adminStats;
        $this->imageRepository = $imageRepository;
    }
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        return $this->render(
            'admin/index.html.twig',
            [
                'birthstats' => $this->adminStats->getBirthStats(),
                'registrations' => $this->adminStats->getRegistrationsPerDays(),
            ]
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Lorem Ipsum Dashboard');
    }

    public function configureMenuItems(): iterable
    {
        $menuItem = new SubMenuItem('Pagination', 'fa fa-paper');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('User', 'fa fa-user-circle', User::class)->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Section', 'fas fa-list', Forum::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Category', 'fas fa-list', Category::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Group', 'fas fa-users', Group::class)->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::subMenu('Settings', 'fa fa-gear')->setSubItems([
            MenuItem::linkToRoute('Default Profile Image', 'fa fa-image-portrait', 'app_settings_index'),
            MenuItem::linkToRoute('Pagination', 'fa fa-file-lines', 'app_settings_pagination')

        ])->setPermission('ROLE_SUPER_ADMIN');
    }
    public function configureUserMenu(UserInterface $user): UserMenu
    {


 
        return parent::configureUserMenu($user)

            ->displayUserName(true)


            ->displayUserAvatar(true)

            ->addMenuItems([
                MenuItem::linkToRoute('My Profile', 'fa fa-id-card', 'app_profile_settings'),
            ]);
    }
    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addWebpackEncoreEntry('admin');
    }
}
