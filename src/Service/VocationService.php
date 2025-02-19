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
        private int                 $workHoursPerDay,

    )
    {
    }


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
        $vacationPerMonth =  $this->vacationDaysPerYear / 12.0; // 2.0833 days/month
        $interval = $startDate->diff($endDate);
        $monthsWorked = $interval->y * 12 + $interval->m;
        $daysInFirstMonth = (int) $startDate->format('t');
        $daysWorkedFirstMonth = $daysInFirstMonth - (int) $startDate->format('d') + 1;
        $firstMonthAccrual = ($daysWorkedFirstMonth / $daysInFirstMonth) * $vacationPerMonth;
        $totalVacationDaysUntilEndOfYear  = ($monthsWorked * $vacationPerMonth) + $firstMonthAccrual;
        return $this->roundToHalf($totalVacationDaysUntilEndOfYear);
    }

    private function roundToHalf($number): float
    {
        $integerPart = floor($number);
        $decimalPart = $number - $integerPart;
        if ($decimalPart < 0.5) {
            return $integerPart;
        } else {
            return $integerPart + 0.5;
        }
    }


    public function increaseEmployeeAvailableVacationDays (Employee $employee, int $days): void
    {
        $employee->getAvailableVocationDays() ?  : $this->calculateEmployeeAvailableVacationDays($employee);
        $employee->setAvailableVocationDays($employee->getAvailableVocationDays() + $days);
    }

    public function canDecreaseEmployeeAvailableVacationDays(Employee $employee, int $days): bool
    {
        $employee->getAvailableVocationDays() ?  : $this->calculateEmployeeAvailableVacationDays($employee);
        $leftVacationDays = $employee->getAvailableVocationDays() - $days ;
        if ($leftVacationDays < 0 ){
            return false;
        }
        $employee->setAvailableVocationDays($leftVacationDays);
        return true;

    }

}