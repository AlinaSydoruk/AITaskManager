<?php

namespace App\Validator;

use App\Repository\AbsenceRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AbsencePeriodValidator extends ConstraintValidator
{

    public function __construct(
        private AbsenceRepository $absenceRepository
    )
    {
    }


    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var AbsencePeriod $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (!$constraint instanceof AbsencePeriod) {
            throw new UnexpectedTypeException($constraint, AbsencePeriod::class);
        }


        $employee = $value->getEmployee();
        $startDate = $value->getStartDate();
        $endDate = $value->getEndDate();


        if (!$employee || !$startDate || !$endDate) {
            return;
        }

        $overlappingAbsences = $this->absenceRepository->findOverlappingAbsences($employee, $startDate, $endDate);


        if (!empty($overlappingAbsences)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ startDate }}', $startDate->format('d-m-Y'))
                ->setParameter('{{ endDate }}', $endDate->format('d-m-Y'))
                ->addViolation();
        }
    }
}
