<?php

namespace App\Service;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class VocationService
{
    public function __construct(
        private EmployeeRepository  $employeeRepository,
        private TranslatorInterface $translator,
        private int                 $vacationDaysPerYear,
        private int                 $workHoursPerDay

    )
    {
    }

    public function getVacationHoursPerYear(): int
    {

        return $this->vacationDaysPerYear * $this->workHoursPerDay;
    }

    public function calculateEmployeeAvailableVacationDays(Employee $employee): float
    {
        return $employee->getAvailableVocationHours() / $this->workHoursPerDay;
    }

    public function increaseEmployeeAvailableVacationDays (Employee $employee, int $hours): void
    {
        $employee->setAvailableVocationHours($employee->getAvailableVocationHours() + $hours);
    }

    public function decreaseEmployeeAvailableVacationHours (Employee $employee, int $hours): bool
    {
        $leftVacationHours = $employee->getAvailableVocationHours() - $hours ;
        if ($leftVacationHours < 0 ){
            return false;
        }
        $employee->setAvailableVocationHours($leftVacationHours);
        return true;

    }

}