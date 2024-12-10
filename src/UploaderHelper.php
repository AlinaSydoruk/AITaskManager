<?php

namespace App;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class UploaderHelper
{
    public function __construct(
        private ParameterBagInterface $parameterBag
    )
    {
    }

    public function getPublicDownloadsPath(): string
    {
        return $this->parameterBag->get('kernel.project_dir') . '/public/downloads/';
    }

    public function getUploadsPath(): string
    {
        return $this->parameterBag->get('kernel.project_dir') . '/var/uploads/';
    }
    public function createUniqueFilename(UploadedFile  $uploadedFile): string
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        return  Urlizer::urlize($originalFilename) . '-' .  uniqid() . '.' . $uploadedFile->guessClientExtension();
    }

}