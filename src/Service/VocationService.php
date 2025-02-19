<?php

namespace App\Service;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class VocationService
{
    public function __construct(
        private EmployeeRepository  $employeeRepository,
        private TranslatorInterface $translator,
        private int                 $vacationDaysPerYear,
        private int                 $workHoursPerDay

    )
    {
    }
    private float $vacationPerMonth = 25.0 / 12.0; // 2.0833 days/month

    public function getVacationDaysPerYear(): int
    {
        return $this->vacationDaysPerYear;
    }

    public function calculateEmployeeAvailableVacationDays(Employee $employee): float
    {
        $currentDate = new \DateTime('today');
        $endOfYear = new \DateTime('December 31');


        if (!$employee->getAvailableVocationDays()) {
            if ($employee->getFirstWorkingDay() >= $currentDate) { // Not yet started working

                //The number of vocation days until the end of the year
                $totalVacationDaysUntilEndOfYear = $this->getEmployeeVocationDaysInPeriod($employee->getFirstWorkingDay(), $endOfYear);
                $employee->setAvailableVocationDays($totalVacationDaysUntilEndOfYear);
                return $totalVacationDaysUntilEndOfYear;

            }else{
                $totalVacationDays = $this->getEmployeeVocationDaysInPeriod($employee->getFirstWorkingDay() , $currentDate);
                $totalUsedVocationDays = null;
                foreach ($employee->getAbsences() as $absence){
                    $totalUsedVocationDays+=$absence->getDurationInDays();
                }
                $availableVocationDays = $totalVacationDays - $totalUsedVocationDays; // can be negative
                $employee->setAvailableVocationDays($availableVocationDays);
                return $availableVocationDays;
            }
        }
        return $employee->getAvailableVocationDays();
    }


    private function getEmployeeVocationDaysInPeriod(\DateTimeInterface $startDate , \DateTimeInterface $endDate ) : float
    {
        $interval = $startDate->diff($endDate);
        $monthsWorked = $interval->y * 12 + $interval->m;
        $daysInFirstMonth = (int) $startDate->format('t');
        $daysWorkedFirstMonth = $daysInFirstMonth - (int) $startDate->format('d') + 1;
        $firstMonthAccrual = ($daysWorkedFirstMonth / $daysInFirstMonth) * $this->vacationPerMonth;
        $totalVacationDaysUntilEndOfYear  = ($monthsWorked * $this->vacationPerMonth) + $firstMonthAccrual;
        // 1.343434  $totalVacationDaysUntilEndOfYear  - do not forget to round it  !!!!
        return $totalVacationDaysUntilEndOfYear;
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