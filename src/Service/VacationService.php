<?php

namespace App\Service;

use App\Entity\Absence;
use App\Entity\Employee;
use Symfony\Contracts\Translation\TranslatorInterface;

class VacationService
{
    public function __construct(
        private TranslatorInterface $translator,
        private int                 $vacationDaysPerYear,
        private string              $endOfYear,
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
        $endOfThisYear = \DateTime::createFromFormat('d-m',$this->endOfYear);

        if (!$endOfThisYear) {
            throw new \InvalidArgumentException($this->translator->trans('error.invalid_end_of_year_format_expected_d-m'));
        }
        if ($employee->getFirstWorkingDay() > $currentDate) {
            $endOfYearOfFirstWorkingDay = \DateTime::createFromFormat( 'd-m-Y',$this->endOfYear . '-' . $employee->getFirstWorkingDay()->format('Y'));
            return $this->getEmployeeVacationDaysInPeriod($employee->getFirstWorkingDay(), $endOfYearOfFirstWorkingDay);
        } else {
            $totalVacationDays = $this->getEmployeeVacationDaysInPeriod($employee->getFirstWorkingDay(), $endOfThisYear);
            $totalUsedVocationDays = null;
            foreach ($employee->getAbsences() as $absence) {
                $totalUsedVocationDays += $absence->getDurationInDays();
            }
            return $totalVacationDays - $totalUsedVocationDays; // can be negative
        }
    }


    private function getEmployeeVacationDaysInPeriod(\DateTimeInterface $startDate , \DateTimeInterface $endDate ) : float
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

    public function canTakeVacation(Absence $absence): bool
    {
        $availableVocationDays = $this->calculateEmployeeAvailableVacationDays($absence->getEmployee());
        if ($availableVocationDays - $absence->getDurationInDays() < 0 ){
            return false;
        }
        return true;
    }
}