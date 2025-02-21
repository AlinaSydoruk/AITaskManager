<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute]
class NoAbsencesBeforeFirstWorkingDay extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */

    public string $message = 'error.you_can_not_change_your_first_working_day_if_there_are_any_absences';
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
