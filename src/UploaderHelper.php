<?php

namespace App;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

readonly class UploaderHelper
{
    public function __construct(
        private ParameterBagInterface $parameterBag
    )
    {
    }
    public function getPublicPathWithFileName(string $fileName): string
    {
        return $this->parameterBag->get('kernel.project_dir') . '/public/uploads/' . $fileName;
    }

}