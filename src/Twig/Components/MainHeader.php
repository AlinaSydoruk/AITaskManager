<?php


namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class MainHeader
{

    public string $text;
    public ?string $subheading = null;

    public string $class = '';

    public ?string $description = null;
}
