<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NoAbsencesBeforeFirstWorkingDayValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var NoAbsencesBeforeFirstWorkingDay $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (!$constraint instanceof NoAbsencesBeforeFirstWorkingDay) {
            throw new UnexpectedTypeException($constraint, NoAbsencesBeforeFirstWorkingDay::class);
        }

        $firstWorkingDay = $value->getFirstWorkingDay();
        if (!$firstWorkingDay) {
            return;
        }
        $absences =$value->getAbsences();

        if ($firstWorkingDay >  new \DateTime('today') && !($value->getAbsences())->isEmpty()){
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}