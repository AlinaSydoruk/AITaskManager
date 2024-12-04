<?php

namespace App\Controller;

use App\Entity\Absence;
use App\Form\AbsenceFormType;
use App\Repository\AbsenceRepository;
use App\Repository\EmployeeRepository;
use App\Service\ExportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/export', name: 'app_export_')]
class ExportController extends AbstractController
{
    public function __construct(
        private readonly ExportService $exportService,
    )
    {}

}
