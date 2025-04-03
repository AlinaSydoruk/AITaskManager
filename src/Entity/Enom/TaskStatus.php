<?php

namespace App\Entity\Enom;

enum TaskStatus: string
{
    case  toDo = 'To Do';
    case  inProgress = 'In Progress';
    case scheduled = 'Scheduled';

    case  blocked = 'Blocked';
    case  done = 'Done';

    case  canceled  = 'Canceled';
    case  needsImprovement = 'Needs Improvement';
    case  onHold = 'On Hold';


    public function getTranslationKey(): string
    {
        return 'task_status.'. str_replace(" ","_" ,strtolower($this->value) );
    }
}