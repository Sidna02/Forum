<?php

namespace App\Repository;

use App\Entity\Image;
use App\Entity\User;
use App\Service\ConfigHandler;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Traversable;
use Vich\UploaderBundle\Entity\File as EntityFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * @extends ServiceEntityRepository<Image>
 *
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    private ConfigHandler $config;
    public function __construct(ManagerRegistry $registry, ConfigHandler $config)
    {
        $this->config = $config;
        parent::__construct($registry, Image::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Image $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Image $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param User[] $users
     * @return array
     * returns users with their corresponding profile picture image paths
     * with user id as key
     */
    public function fetchUsersProfileImage($users): array
    {

        $images = [];
        foreach ($users as $user) {
            $image = $this->findOneBy(['id' => $user->getProfileImage()]);
            dump($this->config);
            dump($image);
            $images[$user->getId()] = $image == null ? ($this->config->getDefaultImagePath()) : ('/images/image/'.$image->getImage()->getName());
        }
        return $images;
    }
}
