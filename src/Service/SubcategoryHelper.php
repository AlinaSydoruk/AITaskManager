<?php

namespace App\Service;

use App\Entity\Subcategory;

class SubcategoryHelper
{
    public function getParentChain(Subcategory $subcategory): array
    {
        $chain = [];
        while ($subcategory && $subcategory->getParent()) {
            $subcategory = $subcategory->getParent();
            $chain[] = $subcategory;
        }

        return array_reverse($chain);
    }
}