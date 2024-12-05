<?php

namespace App\Controller;

use App\Entity\Absence;
use App\Form\AbsenceFormType;
use App\Repository\AbsenceRepository;
use App\Repository\EmployeeRepository;
use App\Service\ExportService;
use App\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/download', name: 'app_download_')]
class DownloadController extends AbstractController
{
    public function __construct(
        private readonly ExportService $exportService,
    )
    {
    }

    #[Route('/employees', name: 'employees')]
    public function downloadEmployees(UploaderHelper $uploaderHelper): Response
    {
        $fileName = $this->exportService->exportEmployeeData('employee.xlsx');
        $filePath = $uploaderHelper->getPublicPathWithFileName($fileName);
        return $this->file($filePath, $fileName);
    }
}
