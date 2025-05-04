<?php


namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Section
{
    public ?string $title = null ;

    public string $class = '';

}
