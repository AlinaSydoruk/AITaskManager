<?php

namespace App\Validator;

use App\Repository\AbsenceRepository;
use App\Service\VacationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EmptyAbsencePeriodValidator extends ConstraintValidator
{

    public function __construct(
        private  VacationService     $vacationService
    )
    {
    }


    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var EmptyAbsencePeriod $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (!$constraint instanceof EmptyAbsencePeriod) {
            throw new UnexpectedTypeException($constraint, EmptyAbsencePeriod::class);
        }


        $startDate = $value->getStartDate();
        $isStartDateHalfDay = $value->isStartDateHalfDay();
        $endDate = $value->getEndDate();
        $isEndDateHalfDay = $value->isEndDateHalfDay();

        if (!$startDate || !$isStartDateHalfDay || !$endDate || !$isEndDateHalfDay) {
            return;
        }

        if ($this->vacationService->getDurationInDaysWithoutHolidaysAndWeekends($startDate, $endDate, $isStartDateHalfDay, $isEndDateHalfDay) == 0 )
            $this->context->buildViolation($constraint->message)
                ->addViolation();

    }
}
