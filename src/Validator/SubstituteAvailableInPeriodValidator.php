<?php

namespace App\Validator;

use App\Repository\AbsenceRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SubstituteAvailableInPeriodValidator extends ConstraintValidator
{

    public function __construct(
        private AbsenceRepository $absenceRepository
    )
    {
    }


    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var SubstituteAvailableInPeriod $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (!$constraint instanceof SubstituteAvailableInPeriod) {
            throw new UnexpectedTypeException($constraint, SubstituteAvailableInPeriod::class);
        }


        $substituteEmployee = $value->getSubstitute();
        $startDate = $value->getStartDate();
        $endDate = $value->getEndDate();

        if (!$substituteEmployee || !$startDate || !$endDate) {
            return;
        }

        $overlappingAbsences = $this->absenceRepository->findOverlappingAbsences($substituteEmployee, $startDate, $endDate);
        if (!empty($overlappingAbsences)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ startDate }}', $startDate->format('d-m-Y'))
                ->setParameter('{{ endDate }}', $endDate->format('d-m-Y'))
                ->addViolation();
        }
    }
}
