<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ReportsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReportsRepository::class)]
#[ApiResource]
class Reports
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "datetime_immutable", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: "integer", options: ["default" => 1])]
    #[Assert\Range(min: 1, max: 3)]
    private ?int $status = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reports')]
    #[ORM\JoinColumn(name: "reporter_id", referencedColumnName: "id", nullable: false)]
    private ?User $reporter = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'reports')]
    #[ORM\JoinColumn(name: "reported_post_id", referencedColumnName: "id", nullable: false)]
    private ?Post $reportedPost = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getReporter(): ?User
    {
        return $this->reporter;
    }

    public function setReporter(?User $reporter): self
    {
        $this->reporter = $reporter;

        return $this;
    }

    public function getReportedPost(): ?Post
    {
        return $this->reportedPost;
    }

    public function setReportedPost(?Post $reportedPost): self
    {
        $this->reportedPost = $reportedPost;

        return $this;
    }
}