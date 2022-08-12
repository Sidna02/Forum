<?php

namespace App\Entity;

use App\Repository\TopicRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[ORM\Entity(repositoryClass: TopicRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Cache('NONSTRICT_READ_WRITE')]
class Topic extends AbstractPost
{

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title = null;


    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'topics')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\OneToMany(mappedBy: 'topic', targetEntity: Comment::class, cascade: ['persist'])]
    private Collection $comments;



    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool $isLocked = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $lastActivity;


    public function __construct(User $user, Category $category)
    {
        $this->author = $user;
        $this->comments = new ArrayCollection();
        $this->category = $category;
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

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTopic($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTopic() === $this) {
                $comment->setTopic(null);
            }
        }

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

    public function getIsLocked(): ?bool
    {
        return $this->isLocked;
    }

    public function setIsLocked(?bool $isLocked): self
    {
        $this->isLocked = $isLocked;

        return $this;
    }


    public function getType()
    {
        return "topic";
    }

    public function getLastActivity(): ?\DateTimeInterface
    {
        return $this->lastActivity;
    }

    public function setLastActivity(?\DateTimeInterface $lastActivity): self
    {
        $this->lastActivity = $lastActivity;

        return $this;
    }
    public function getLastPostIdentifier(): string
    {
        return $this->title;
    }

}
