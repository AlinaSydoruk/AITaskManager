<?php

namespace App\Entity\Enom;

enum TaskPriority: string
{
    case low = 'Low';
    case medium = 'Medium';
    case high = 'High';
    case critical = 'Critical';

}