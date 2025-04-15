<?php

namespace App\Entity\Enom;

enum TaskSubstatus: string
{
    case scheduled = 'Scheduled';

    case  blocked = 'Blocked';
    case  canceled  = 'Canceled';
    case  needsImprovement = 'Needs Improvement';
    case  onHold = 'On Hold';


    public function getTranslationKey(): string
    {
        return 'task_status.'. str_replace(" ","_" ,strtolower($this->value) );
    }
}