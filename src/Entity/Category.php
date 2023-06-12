<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource()]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[UniqueEntity('title', 'Il en existe déjà un')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;

    #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'categories')]
    private Collection $Articles;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    private ?Theme $Theme = null;

    public function __construct()
    {
        $this->Articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getArticles(): Collection
    {
        return $this->Articles;
    }

    public function addArticle(Post $article): static
    {
        if (!$this->Articles->contains($article)) {
            $this->Articles->add($article);
        }

        return $this;
    }

    public function removeArticle(Post $article): static
    {
        $this->Articles->removeElement($article);

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->Theme;
    }

    public function setTheme(?Theme $Theme): static
    {
        $this->Theme = $Theme;

        return $this;
    }
}
