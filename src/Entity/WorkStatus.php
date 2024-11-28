<?php

namespace App\Entity;

enum WorkStatus: string
{
    case  notYetStartedWorking = 'Not yet started working';
    case  working = 'Working';
    case  dismissed = 'Dismissed';
}
