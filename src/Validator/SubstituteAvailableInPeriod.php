<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute]
class SubstituteAvailableInPeriod extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */

    public string $message = 'error.please_select_another_substitute_as_this_person_will_be_absent';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
