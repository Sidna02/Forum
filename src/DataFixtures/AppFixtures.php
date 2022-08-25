<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Forum;
use App\Entity\Image;
use App\Entity\Topic;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Vich\UploaderBundle\FileAbstraction\ReplacingFile;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;
    /**
     * @var User[]
     */
    private array $users;
    /**
     * @var Forum[]
     */
    private array $forums;
    /**
     * @var Category[]
     */
    private array $categories;
    /**
     * @var Topic[]
     */
    private array $topics;
    private Filesystem $filesystem;

    public function __construct(UserPasswordHasherInterface $hasher, Filesystem $filesystem)
    {
        $this->hasher = $hasher;
        $this->filesystem = $filesystem;
    }


    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadForums($manager);
        $this->loadCategories($manager);
        $this->loadTopics($manager);
        $this->loadComments($manager);
    }
    public function loadUsers(ObjectManager $manager)
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
                new DateTimeImmutable(mt_rand(1920, 2004) . '-' . mt_rand(1, 11) . '-' . mt_rand(1, 11))
            );
        $manager->persist($admin);
        $admin->setRegisteredAt(new DateTimeImmutable('2022-01-07'));

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
            new DateTimeImmutable('2022-01-07')
        );
        $users = [];
        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            $user
                ->setEmail(mt_rand(12348979, 8971234456) . '@lemarocazzeza.ma')
                ->setUsername('user' . mt_rand(1045, 8764546))
                ->setIsVerified(0)
                ->setBirthdate(
                    new DateTime(mt_rand(1920, 2004) . '-' . mt_rand(1, 11) . '-' . mt_rand(1, 11))
                )
                ->setPassword($this->hasher->hashPassword($user, '123456789'));

            $manager->persist($user);
            $user->setRegisteredAt(
                new DateTimeImmutable(mt_rand(2019, 2022) . '-' . mt_rand(4, 6) . '-' . mt_rand(8, 12))
            );
            $users[] = $user;
            $image = new Image();
            $file =  $this->downloadFile($this->getImageLinks()[mt_rand(0, 4)]);
            $image->setImageFile($file);
            $manager->persist($image);
            $manager->flush();
            $image->getId();
            $user->setProfileImage($image);
            $this->filesystem->remove($file->getPath());
        }
        $manager->flush();
        $this->users = $users;
    }
    public function loadForums(ObjectManager $manager)
    {
        $forums = [];
        foreach ($this->getForums() as $forumTitle) {
            $forum = new Forum();
            $forum->setTitle($forumTitle);
            $manager->persist($forum);
            $forums[] = $forum;
        }
        $this->forums = $forums;
    }
    public function loadCategories(ObjectManager $manager)
    {
        $categories = $this->getCategories();
        $forumIteration=0;
        $catIteration = 0;
        $eCategories = [];
        foreach ($categories as $category) {
            $eCat = new Category();
            $eCat->setTitle($category);
            ($this->forums[$forumIteration])->addCategory($eCat);
            $catIteration++;
            if ($catIteration % 3 == 0) {
                $forumIteration++;
            }
            $eCategories[] = $eCat;
        }
        $manager->flush();
        $this->categories = $eCategories;
    }
    public function loadTopics(ObjectManager $manager)
    {
        $topics = [];
        for ($i=0; $i<200; $i++) {
            $topic = new Topic($this->users[mt_rand(0, count($this->users)-1)], $this->categories[mt_rand(0, count($this->categories)-1)]);
            $topic->setTitle($this->getPhrases()[mt_rand(0, 28)]);
            $topic->setBody(
                $this->getPhrases()[mt_rand(0, 28)].
                $this->getPhrases()[mt_rand(0, 28)].
                $this->getPhrases()[mt_rand(0, 28)].
                $this->getPhrases()[mt_rand(0, 28)].
                $this->getPhrases()[mt_rand(0, 28)].
                $this->getPhrases()[mt_rand(0, 28)].
                $this->getPhrases()[mt_rand(0, 28)]
            );


            $manager->persist($topic);
            $topics[] = $topic;
        }

        $manager->flush();
        $this->topics = $topics;
    }
    public function loadComments(ObjectManager $manager)
    {
        for ($i = 0; $i<1000; $i++) {
            $comment = new Comment($this->users[mt_rand(0, 99)], $this->topics[mt_rand(0, 99)]);
            $comment->setBody(
                $this->getPhrases()[mt_rand(0, 28)].
                $this->getPhrases()[mt_rand(0, 28)].
                $this->getPhrases()[mt_rand(0, 28)].
                $this->getPhrases()[mt_rand(0, 28)]
            );
            $manager->persist($comment);
        }
        $manager->flush();
    }
    private function getForums(): array
    {

        return ['Community',
                'Staff',
                'General',
            'Archive'
            ];
    }
    private function getCategories(): array
    {
        return ['News and Announcements',
                'Community Feedback',
                'Suggestions',
                'Wiki discussion',
                'Staff Discussion',
                'Staff help',
                'Staff information',
                'General Discussion',
                'Off-topic',
                'Introductions',
            ];
    }
    private function getPhrases(): array
    {
        return [
            'Lorem ipsum dolor sit amet consectetur adipiscing elit',
            'Pellentesque vitae velit ex',
            'Mauris dapibus risus quis suscipit vulputate',
            'Eros diam egestas libero eu vulputate risus',
            'In hac habitasse platea dictumst',
            'Morbi tempus commodo mattis',
            'Ut suscipit posuere justo at vulputate',
            'Ut eleifend mauris et risus ultrices egestas',
            'Aliquam sodales odio id eleifend tristique',
            'Urna nisl sollicitudin id varius orci quam id turpis',
            'Nulla porta lobortis ligula vel egestas',
            'Curabitur aliquam euismod dolor non ornare',
            'Sed varius a risus eget aliquam',
            'Nunc viverra elit ac laoreet suscipit',
            'Pellentesque et sapien pulvinar consectetur',
            'Ubi est barbatus nix',
            'Abnobas sunt hilotaes de placidus vita',
            'Ubi est audax amicitia',
            'Eposs sunt solems de superbus fortis',
            'Vae humani generis',
            'Diatrias tolerare tanquam noster caesium',
            'Teres talis saepe tractare de camerarius flavum sensorem',
            'Silva de secundus galatae demitto quadra',
            'Sunt accentores vitare salvus flavum parses',
            'Potus sensim ad ferox abnoba',
            'Sunt seculaes transferre talis camerarius fluctuies',
            'Era brevis ratione est',
            'Sunt torquises imitari velox mirabilis medicinaes',
            'Mineralis persuadere omnes finises desiderium',
            'Bassus fatalis classiss virtualiter transferre de flavum',
        ];
    }
    public function getImageLinks(): array
    {
        return ['https://i.pinimg.com/564x/1e/a5/2b/1ea52bcfbd326891a5249e9bc1d4113b.jpg',
            'https://i.pinimg.com/564x/3a/e1/bf/3ae1bf3be8c771dde9e351554bc68323.jpg',
        'https://i.pinimg.com/736x/e7/38/a4/e738a47f72aafa995d7b28e312323a5f.jpg',
        'https://i.pinimg.com/564x/c6/30/11/c630114101a572e7bc7bde68f01e61a4.jpg',
        'https://garcon-magazine.com/wp-content/uploads/2020/08/mr-propre.jpg'
        ];
    }
    public function downloadFile($url): ReplacingFile
    {
        $file_name = basename($url);

        if (file_put_contents($file_name, file_get_contents($url))) {
            copy($file_name, "public/images/image/".$file_name);
            return new ReplacingFile($file_name);
        } else {
            echo "File downloading failed.";
        }
    }
}
