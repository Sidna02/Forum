<?php

namespace App\Entity;

use App\Repository\TopicRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\Persistence\Event\LifecycleEventArgs;



#[ORM\MappedSuperclass()]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractPost
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(type: 'text')]
    protected ?string $body;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'topics')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?User $author;

    #[ORM\Column(type: 'datetime_immutable')]
    protected ?DateTimeImmutable $createdAt;


    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?DateTimeImmutable $lastEditedAt;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist', 'remove'])]
    protected ?User $lastEditedBy;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }


    public function getLastEditedAt(): ?\DateTimeImmutable
    {
        return $this->lastEditedAt;
    }

    public function setLastEditedAt(?\DateTimeImmutable $lastEditedAt): self
    {
        $this->lastEditedAt = $lastEditedAt;

        return $this;
    }

    public function getLastEditedBy(): ?User
    {
        return $this->lastEditedBy;
    }

    public function setLastEditedBy(?User $lastEditedBy): self
    {
        $this->lastEditedBy = $lastEditedBy;

        return $this;
    }


    #[ORM\PrePersist]
    public function updateCreatedAt(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }
    #[ORM\PreUpdate]
    public function updateEditedAt(): void
    {
        $this->lastEditedAt = new DateTimeImmutable();
    }



    abstract public function getType();


    public function getLastActivity(): ?\DateTimeInterface
    {
        return $this->lastActivity;
    }

    public function setLastActivity(?\DateTimeInterface $lastActivity): self
    {
        $this->lastActivity = $lastActivity;

        return $this;
    }

    abstract public function getLastPostIdentifier(): string;
}