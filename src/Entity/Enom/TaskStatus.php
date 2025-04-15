<?php

namespace App\Entity\Enom;

enum TaskStatus: string
{
    case  toDo = 'To Do';
    case  inProgress = 'In Progress';
    case  done = 'Done';


    public function getTranslationKey(): string
    {
        return 'task_status.'. str_replace(" ","_" ,strtolower($this->value) );
    }
}