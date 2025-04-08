<?php

namespace App\Twig\Extension;

use App\Entity\Subcategory;
use App\Service\SubcategoryHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SubcategoryHelperExtension extends AbstractExtension
{
    public function __construct(
        private SubcategoryHelper $subcategoryHelper
    )
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('subcategory_parent_chain', [$this, 'getSubcategoryParentChain']),
        ];
    }

    public function getSubcategoryParentChain(Subcategory $subcategory): array
    {
        return $this->subcategoryHelper->getParentChain($subcategory);
    }

}