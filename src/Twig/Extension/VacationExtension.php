<?php

namespace App\Twig\Extension;

use App\Entity\Employee;
use App\Service\VacationService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class VacationExtension extends AbstractExtension
{
    public function __construct(
        private VacationService $vacationService
    )
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vacation_days', [$this, 'getVacationDays']),
            new TwigFunction('calculate_employee_available_vacation_Days', [$this, 'calculateEmployeeAvailableVacationDays']),
        ];
    }

    public function getVacationDays(\DateTimeInterface $startDate, \DateTimeInterface $endDate, bool $isStartDateHalfDay, bool $isEndDateHalfDay): float
    {
        return $this->vacationService->getDurationInDaysWithoutHolidaysAndWeekends($startDate, $endDate, $isStartDateHalfDay, $isEndDateHalfDay);
    }
    public function calculateEmployeeAvailableVacationDays(Employee $employee): float
    {
        return $this->vacationService->calculateEmployeeAvailableVacationDays($employee);
    }

}