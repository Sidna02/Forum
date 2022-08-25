<?php
declare(strict_types=1);

namespace App\Twig;

use App\Entity\AbstractPost;
use App\Entity\Comment;
use App\Entity\Topic;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PostUrl extends AbstractExtension
{


    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {


        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('generate_post_url', [$this, 'generatePostUrl']),
        ];
    }

    public function generatePostUrl(AbstractPost $object): string
    {
        if ($object instanceof Topic) {
            return $this->urlGenerator->generate('app_topic_view', ['id'=>$object->getId()]);
        } elseif ($object instanceof Comment) {
            return $this->urlGenerator->generate('app_topic_view', ['id'=>$object->getTopic()->getId()]);
        }
        return '#';
    }
}
