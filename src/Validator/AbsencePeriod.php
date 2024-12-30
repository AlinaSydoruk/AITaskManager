<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute]
class AbsencePeriod extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */

    public string $message = 'The absence period from {{ startDate }} to {{ endDate }} overlaps with an existing one.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
