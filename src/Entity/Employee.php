<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\SoftDeleteable;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints  as  Assert;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[SoftDeleteable(fieldName: 'deletedAt')]
class Employee
{
    use TimestampableEntity,  SoftDeleteableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $firstWorkingDay = null;

    #[Assert\GreaterThanOrEqual ( propertyPath : 'firstWorkingDay' ) ]
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastWorkingDay = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    //#[AssertPhoneNumber(defaultRegion: "CH")]
    private ?string $businessNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    //#[AssertPhoneNumber(defaultRegion: "CH")]
    private ?string $privateNumber = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $streetAndNumber = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $postalCode = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?int $monthlySalary = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $jobTitle = null;

    /**
     * @var Collection<int, Absence>
     */
    #[ORM\OneToMany(targetEntity: Absence::class, mappedBy: 'employee', orphanRemoval: true)]
    private Collection $absences;
    #[ORM\Column(nullable: true)]
    private ?float $availableVocationDays = null;

    public function __construct()
    {
        $this->absences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFirstWorkingDay(): ?\DateTimeInterface
    {
        return $this->firstWorkingDay;
    }

    public function setFirstWorkingDay(?\DateTimeInterface $firstWorkingDay): void
    {
        $this->firstWorkingDay = $firstWorkingDay;
    }

    public function getLastWorkingDay(): ?\DateTimeInterface
    {
        return $this->lastWorkingDay;
    }

    public function setLastWorkingDay(?\DateTimeInterface $lastWorkingDay): void
    {
        $this->lastWorkingDay = $lastWorkingDay;
    }

    public function getWorkStatus(): ?WorkStatus
    {
        $currentDate = new \DateTime('today');

        if ($this->getLastWorkingDay() && $this->getLastWorkingDay() < $currentDate) {
            return WorkStatus:: noLongerWithTheCompany;
        }elseif ($this->getFirstWorkingDay() > $currentDate) {
            return WorkStatus::notYetStartedWorking;
        }elseif ($this->getLastWorkingDay()){
            return WorkStatus:: contractTerminated;
        } else {
            return WorkStatus::working;
        }
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getBusinessNumber(): ?string
    {
        return $this->businessNumber;
    }

    public function setBusinessNumber(?string $businessNumber): void
    {
        $this->businessNumber = $businessNumber;
    }

    public function getPrivateNumber(): ?string
    {
        return $this->privateNumber;
    }

    public function setPrivateNumber(?string $privateNumber): void
    {
        $this->privateNumber = $privateNumber;
    }

    public function getStreetAndNumber(): ?string
    {
        return $this->streetAndNumber;
    }

    public function setStreetAndNumber(?string $streetAndNumber): void
    {
        $this->streetAndNumber = $streetAndNumber;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getMonthlySalary(): ?int
    {
        return $this->monthlySalary;
    }

    public function setMonthlySalary(?int $monthlySalary): void
    {
        $this->monthlySalary = $monthlySalary;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(?string $jobTitle): void
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * @return Collection<int, Absence>
     */
    public function getAbsences(): Collection
    {
        return $this->absences;
    }

    public function addAbsence(Absence $absence): static
    {
        if (!$this->absences->contains($absence)) {
            $this->absences->add($absence);
            $absence->setEmployee($this);
        }

        return $this;
    }

    public function removeAbsence(Absence $absence): static
    {
        if ($this->absences->removeElement($absence)) {
            // set the owning side to null (unless already changed)
            if ($absence->getEmployee() === $this) {
                $absence->setEmployee(null);
            }
        }

        return $this;
    }

    public function getFullName():string
    {
        return $this->getFirstName() . " " . $this->getLastName();
    }

    public function getAvailableVocationDays(): ?float
    {
        return $this->availableVocationDays;
    }

    public function setAvailableVocationDays(?float $availableVocationDays): void
    {
        $this->availableVocationDays = $availableVocationDays;
    }



}
