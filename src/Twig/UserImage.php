<?php

namespace App\Twig;

use App\Entity\User;
use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Service\ConfigHandler;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class UserImage extends AbstractExtension
{
    private ImageRepository $imageRepository;
    private ConfigHandler $configHandler;

    public function __construct(ImageRepository $imageRepository, ConfigHandler $configHandler)
    {

        $this->imageRepository = $imageRepository;
        $this->configHandler = $configHandler;
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('userImagePath', [$this, 'userImagePath']),
        ];
    }

    /**
     * Returns user's profile picture path or default picture
     * @param User $user
     * @return string
     */
    public function userImagePath(User $user): string
    {

        $image = $this->imageRepository->findOneBy(['id' => $user->getProfileImage()]);
        return
            ($image == null ?
                ($this->configHandler->getDefaultImagePath()) : ('/images/image/'.$image->getImage()->getName())
            );
    }
}
