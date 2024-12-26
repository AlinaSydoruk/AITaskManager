<?php

namespace App\Controller;

use App\Entity\Absence;
use App\Form\AbsenceFormType;
use App\Form\UploadFormType;
use App\Repository\AbsenceRepository;
use App\Repository\EmployeeRepository;
use App\Service\ExportService;
use App\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

class ImportExportController extends AbstractController
{
    public function __construct(
        private readonly ExportService $exportService,
        private readonly UploaderHelper $uploaderHelper,
    )
    {
    }

    #[Route('/download/employees', name: 'app_download_employees')]
    public function downloadEmployees(): Response
    {
        $fileName = $this->exportService->exportEmployeeData('employee.xlsx');
        $filePath = $this->uploaderHelper->getPublicDownloadsPath() . $fileName;
        return $this->file($filePath, $fileName);
    }

    #[Route('/upload/absences', name: 'app_upload_absences')]
    public function index(Request $request): Response
    {
        $uploadForm = $this->createForm(UploadFormType::class);
        $uploadForm->handleRequest($request);
        if ($uploadForm->isSubmitted() && $uploadForm->isValid()) {
            /**@var UploadedFile $uploadedFile */
            $uploadedFile = $uploadForm['uploadFile']->getData();
            try {
                $this->uploaderHelper->UploadExcelFile($uploadedFile);
            }catch (\LogicException $exception){
                $uploadForm->addError(new FormError($exception->getMessage()));
                return $this->render('upload/index.html.twig',[
                    'uploadForm' => $uploadForm,
                ]);
            }
            $this->addFlash('success' , 'absences uploaded successfully');
            return $this->redirectToRoute('app_employee_index');
        }
        return $this->render('upload/index.html.twig',[
            'uploadForm' => $uploadForm,
        ]);
    }
}
