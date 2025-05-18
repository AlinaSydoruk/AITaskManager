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

    #[ORM\Column(enumType: TaskPriority::class)]
    #[Assert\NotBlank]
    private ?TaskPriority $taskPriority = null;

    #[ORM\Column(nullable: false, enumType: TaskStatus::class)]
    #[Assert\NotBlank]
    private ?TaskStatus $taskStatus = null;

    #[ORM\ManyToOne(targetEntity: Board::class, cascade: ['persist'], inversedBy: "tasks")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Board $board = null;

    #[ORM\ManyToOne(targetEntity: Subcategory::class, inversedBy: "tasks")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Subcategory $subcategory = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?int $userId;

    public function __construct(?int $userId)
    {
        $this->userId = $userId;
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

    public function getApproximateEstimate(): ?int
    {
        return $this->approximateEstimate;
    }

    public function setApproximateEstimate(?int $approximateEstimate): void
    {
        $this->approximateEstimate = $approximateEstimate;
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

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'board_title' => $this->board?->getTitle(),
            'description' => $this->getDescription(),
            'deadline' => $this->getDeadline()?->format('Y-m-d H:i'),
            'estimate' => $this->getApproximateEstimate(),
            'priority' => $this->getTaskPriority()?->value,
            'status' => $this->getTaskStatus()?->value,
            'scheduled_for' => $this->getScheduledForDate()?->format('Y-m-d H:i'),
            'userId'=>$this->userId,
        ];
    }
}
