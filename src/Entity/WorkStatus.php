<?php

namespace App\Entity;

enum WorkStatus: string
{
    case  notYetStartedWorking = 'Arbeit noch nicht';
    case  working = 'Arbeitet';
    case  contractTerminated = 'Gekündigt';
    case  noLongerWithTheCompany = 'Gegangen';
}
