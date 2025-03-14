<?php


namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class BoardCard
{
    public ?string $class = null;

    public ?string $title = null;

    public ?string $color = null;

    public ?string $url = null;

}
