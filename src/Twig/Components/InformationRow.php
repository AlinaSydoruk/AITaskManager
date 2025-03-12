<?php


namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class InformationRow
{

    public string $title;

    public string $class = '';
    public bool $itemsCenter = false;

    public bool $hoverEffect = true;

    public string $firstColumnWidth = '';
    public string $secondColumnWidth = '';

}
