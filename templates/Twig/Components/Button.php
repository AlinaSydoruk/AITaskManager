<?php


namespace templates\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Button
{

    public string $label;

    public ?string $class = null;

    public ?string $url = null;




}
