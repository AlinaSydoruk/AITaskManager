<?php

namespace App;

use App\DTO\UploadExcelDTO;
use App\Service\ImportService;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class UploaderHelper
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private string                $publicDownloadsDir,
        private string                $uploadsDir,
        private ImportService         $importService,
    )
    {
    }

    public function getPublicDownloadsPath(): string
    {
        return $this->publicDownloadsDir;
    }

    public function getUploadsPath(): string
    {
        return $this->uploadsDir;
    }
    public function createUniqueFilename(UploadedFile  $uploadedFile): string
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        return  Urlizer::urlize($originalFilename) . '-' .  uniqid() . '.' . $uploadedFile->guessClientExtension();
    }
    public function UploadExcelFile(UploadedFile $uploadedFile): void
    {
        $newFileName = $this->createUniqueFilename($uploadedFile);
        $uploadedFile->move($this->getUploadsPath(), $newFileName);
        $this->importService->importAbsences($this->getUploadsPath() . $newFileName);

    }

}