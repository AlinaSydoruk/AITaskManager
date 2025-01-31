<?php

namespace App\Entity;

enum AbsenceType: string
{
    case vacation = 'Ferien';
    case  parenthood = 'Vater-/Mutterschaftsurlaub';
    case unpaidVacation = 'Unbezahlter Urlaub';
    case  military = 'Militär';
    case  civilProtection = 'Zivilschutz';


/*
    case vacation = 'Vacation';
    case  military = 'Military';
    case civilProtection = 'Civil protection';
    case  parenthood = 'Paternity/maternity leave';
    case  unpaidVacation = 'Unpaid vacation';

    */

}