<?php
declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
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
    #[ORM\Cache('NONSTRICT_READ_WRITE')]
    protected ?User $author;

    #[ORM\Column(type: 'datetime_immutable')]
    protected ?DateTimeImmutable $createdAt;


    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?DateTimeImmutable $lastEditedAt;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\Cache('NONSTRICT_READ_WRITE')]
    protected ?User $lastEditedBy;



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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    public function getLastEditedAt(): ?DateTimeImmutable
    {
        return $this->lastEditedAt;
    }

    public function setLastEditedAt(?DateTimeImmutable $lastEditedAt): self
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



    abstract public function getLastPostIdentifier(): string;
}
