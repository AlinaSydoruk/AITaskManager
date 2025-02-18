<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class BreadCrumbItem
{
    public string $link;

    public string $label;

    public bool $isActive = false;
}
