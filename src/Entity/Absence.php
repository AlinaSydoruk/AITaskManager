<?php

namespace App\Entity;

use App\Repository\AbsenceRepository;
use App\Validator\AbsencePeriod;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints  as  Assert;

#[ORM\Entity(repositoryClass: AbsenceRepository::class)]
#[AbsencePeriod]
class Absence
{
    public function __construct(?Employee $employee)
    {
        $this->employee = $employee;
    }
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $startDate = null;

    #[Assert\GreaterThanOrEqual ( propertyPath : 'startDate' ) ]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?AbsenceType $absenceType = null;

    #[ORM\ManyToOne(inversedBy: 'absences')]
    #[ORM\JoinColumn(nullable: false)]
    private Employee $employee;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getAbsenceType(): ?AbsenceType
    {
        return $this->absenceType;
    }

    public function setAbsenceType(?AbsenceType $absenceType): void
    {
        $this->absenceType = $absenceType;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

        return $this;
    }
}
