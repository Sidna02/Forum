<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Forum;
use App\Entity\Topic;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('omarmoulhanout@lemaroc.ma')
            ->setFirstName('Omar')
            ->setLastName('Moul Lhanout')
            ->setUsername('omar1234')
            ->setPassword($this->hasher->hashPassword($admin, '123456789'))
            ->setBirthdate(
                new DateTime(mt_rand(1920, 2004) . '-' . mt_rand(1, 11) . '-' . mt_rand(1, 11))

            );
        $manager->persist($admin);
        $admin->setRegisteredAt(new DateTimeImmutable('2008-11-08'));

        $superadmin = new User();
        $superadmin
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setEmail('brahimboule7ya@lemaroc.ma')
            ->setFirstName('Brahim')
            ->setLastName('Bou Lehya')
            ->setUsername('brahim1234')
            ->setPassword($this->hasher->hashPassword($admin, '123456789'))
            ->setBirthdate(
                new DateTime(mt_rand(1920, 2004) . '-' . mt_rand(1, 11) . '-' . mt_rand(1, 11))

            );
        $manager->persist($superadmin);
        $superadmin->setRegisteredAt(
            new DateTimeImmutable(mt_rand(1920, 2004) . '-' . mt_rand(1, 11) . '-' . mt_rand(1, 11))

        );
        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            $user
                ->setEmail(mt_rand(12348979, 8971234456) . '@lemarocazzeza.ma')
                ->setUsername('user' . mt_rand(1045, 8764546))
                ->setIsVerified(0)
                ->setBirthdate(
                    new DateTime(mt_rand(1920, 2004) . '-' . mt_rand(1, 11) . '-' . mt_rand(1, 11))

                )
                ->setPassword($this->hasher->hashPassword($user, 'dummyuser'));
            $manager->persist($user);
            $user->setRegisteredAt(
                new DateTimeImmutable(mt_rand(2019, 2022) . '-' . mt_rand(4, 6) . '-' . mt_rand(8, 12))

            );
        }


        $announcement = (new Forum())->setTitle('Announcements');
        $announcement->addCategory((new Category())->setTitle('News'))
            ->addCategory((new Category())->setTitle('Updates'));;
        $complaints = (new Forum())->setTitle('Complaints');
        $complaints->addCategory((new Category())->setTitle('Staff Complaints'))
            ->addCategory((new Category())->setTitle('Member Reports'));
        $manager->persist($announcement);
        $manager->persist($complaints);
        $manager->flush();
    }
}
