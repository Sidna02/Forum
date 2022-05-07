<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $body;

    #[ORM\ManyToOne(targetEntity: Topic::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private $topic;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $author;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $createdAt;
    public function __construct(User $user, Topic $topic)
    {
        $this->author = $user;
        $this->topic = $topic;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBody(): ?string
    {
        return $this->body;
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    public function getType()
    {
        return "comment";
    }
    #[ORM\PrePersist]
    public function updateLastActivity()
    {
        $this->topic->setLastActivity(new DateTimeImmutable());
    }

    
}
