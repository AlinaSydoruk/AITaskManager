<?php


namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Row
{

    public string $title;

    public string $class = '';
    public bool $hoverEffect = true;

}
