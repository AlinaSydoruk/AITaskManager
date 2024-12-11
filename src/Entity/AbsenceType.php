<?php

namespace App\Entity;

enum AbsenceType: string
{
    case vacation = 'Vacation';
    case  military = 'Military';
    case  parenthood = 'Paternity/maternity leave';
    case  unpaidVacation = 'Unpaid vacation';
}