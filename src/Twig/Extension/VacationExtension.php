<?php

namespace App\Twig\Extension;

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
        ];
    }

    public function getVacationDays(\DateTimeInterface $startDate, \DateTimeInterface $endDate, bool $isStartDateHalfDay, bool $isEndDateHalfDay): float
    {
        return $this->vacationService->getDurationInDaysWithoutHolidaysAndWeekends($startDate, $endDate, $isStartDateHalfDay, $isEndDateHalfDay);
    }


}