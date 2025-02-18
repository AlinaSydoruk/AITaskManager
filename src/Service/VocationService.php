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

    public function getVacationDaysPerYear(): int
    {
        return $this->vacationDaysPerYear;
    }

    public function calculateEmployeeAvailableVacationDays(Employee $employee): float
    {
        $vacationPerMonth = 25.0 / 12.0; // 2.0833 days/month

        if (!$employee->getAvailableVocationDays()){
            $currentDate = new \DateTime('today');
            if ($employee->getFirstWorkingDay() > $currentDate){
                $availableVocationDays =

            }
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