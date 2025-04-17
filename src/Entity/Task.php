<?php

namespace App\Entity;

use App\Entity\Enom\TaskPriority;
use App\Entity\Enom\TaskStatus;
use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\SoftDeleteable;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[SoftDeleteable(fieldName: 'deletedAt')]
class Task
{
    use TimestampableEntity, SoftDeleteableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(length: 1024 , nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deadline = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?int $approximateEstimate = 30;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $scheduledForDate = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?TaskPriority $taskPriority = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank]
    private ?TaskStatus $taskStatus = null;

    #[ORM\ManyToOne(targetEntity: Board::class, inversedBy: "tasks")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Board $board = null;

    #[ORM\ManyToOne(targetEntity: Subcategory::class, inversedBy: "tasks")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Subcategory $subcategory = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getApproximateEstimate(): ?string
    {
        return $this->approximateEstimate;
    }

    public function setApproximateEstimate(string $approximateEstimate): static
    {
        $this->approximateEstimate = $approximateEstimate;

        return $this;
    }

    public function getScheduledForDate(): ?\DateTimeInterface
    {
        return $this->scheduledForDate;
    }

    public function setScheduledForDate(?\DateTimeInterface $scheduledForDate): static
    {
        $this->scheduledForDate = $scheduledForDate;

        return $this;
    }

    public function getTaskPriority(): ?TaskPriority
    {
        return $this->taskPriority;
    }

    public function setTaskPriority(?TaskPriority $taskPriority): void
    {
        $this->taskPriority = $taskPriority;
    }

    public function getTaskStatus(): ?TaskStatus
    {
        return $this->taskStatus;
    }

    public function setTaskStatus(?TaskStatus $taskStatus): void
    {
        $this->taskStatus = $taskStatus;
    }

    public function getBoard(): ?Board
    {
        return $this->board;
    }

    public function setBoard(?Board $board): self
    {
        $this->board = $board;
        return $this;
    }


    public function getSubcategory(): ?Subcategory
    {
        return $this->subcategory;
    }

    public function setSubcategory(?Subcategory $subcategory): self
    {
        $this->subcategory = $subcategory;
        return $this;
    }

}
