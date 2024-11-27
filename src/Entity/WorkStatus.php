<?php

namespace App\Entity;

enum WorkStatus: string
{
    public const  notYetStartedWorking = 'Not yet started working';

    public const  working = 'Working';

    public const  dismissed = 'Dismissed';
}
