<?php

namespace App\Service;

use App\Entity\Absence;
use App\Entity\AbsenceType;
use App\Entity\Employee;
use App\Repository\AbsenceRepository;
use App\Repository\HolidayRepository;
use DateTimeInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AbsenceService
{

    public function getAbsencesSortedByYear(Employee $employee) :array
    {
        $sortedAbsences = [];
        $absences = $employee->getAbsences();

        foreach ($absences as $absence) {
            $yearStart = $absence->getStartDate()->format('Y');
            $yearEnd = $absence->getEndDate()->format('Y');
            if (!array_key_exists($yearStart, $sortedAbsences)) {
                $sortedAbsences[$yearStart] = [];
            }
            $sortedAbsences[$yearStart][] = $absence;
            if (!$yearStart == $yearEnd) {
                if (!array_key_exists($yearEnd, $sortedAbsences)) {
                    $sortedAbsences[$yearEnd] = [];
                }
                $sortedAbsences[$yearEnd][] = $absence;
            }

        }
        return $sortedAbsences;
    }
}