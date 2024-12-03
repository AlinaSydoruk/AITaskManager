<?php

namespace App\Entity;

enum AbsenceType: string
{
    case vocation = 'Vacation';
    case  military = 'Military';
    case  parenthood = 'Paternity/maternity leave';
    case  unpaidVacation = 'Unpaid vacation';
}