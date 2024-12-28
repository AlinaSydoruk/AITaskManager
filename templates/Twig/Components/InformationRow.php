<?php


namespace templates\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class InformationRow
{

    public string $title;

    public string $class = '';
}
