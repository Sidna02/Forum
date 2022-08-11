<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Cache]
class Comment extends AbstractPost
{

    #[ORM\ManyToOne(targetEntity: Topic::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private Topic $topic;


    public function __construct(User $user, Topic $topic)
    {
        $this->author = $user;
        $this->topic = $topic;
        $this->createdAt = new DateTimeImmutable();
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getType(): string
    {
        return "comment";
    }

    public function getLastPostIdentifier(): string
    {
        return $this->getBody();
    }

    public function getBody(): ?string
    {
        return $this->body;
    }


}
