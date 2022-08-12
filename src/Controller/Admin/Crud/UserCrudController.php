<?php

namespace App\Controller\Admin\Crud;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use RolesField;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasherInterface;

    public function __construct(UserPasswordHasherInterface $passwordHasherInterface)
    {
        $this->passwordHasherInterface = $passwordHasherInterface;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $roles = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_MODERATOR', 'ROLE_USER'];

        return [

            EmailField::new('email'),
            TextField::new('username'),
            TextField::new('password')->onlyWhenCreating(),
            ChoiceField::new('roles')
                ->setChoices(array_combine($roles, $roles))
                ->allowMultipleChoices()
                ->renderExpanded(),
            TextField::new('firstName')->setColumns('col-sm-6 col-lg-5 col-xxl-3'),
            TextField::new('lastName')->setColumns('col-sm-6 col-lg-5 col-xxl-3'),
            DateField::new('birthdate'),
            DateTimeField::new('registeredAt'),


        ];
    }

    /**
     * @param User $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {

        $entityInstance->setPassword(
            $this->passwordHasherInterface->hashPassword($entityInstance, $entityInstance->getPassword())
        );

        parent::persistEntity($entityManager, $entityInstance);
    }

}
