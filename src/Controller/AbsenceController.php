<?php

namespace App\Controller;

use App\Entity\Absence;
use App\Form\AbsenceFormType;
use App\Service\AbsenceService;
use App\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/absence', name: 'app_absence_')]
class AbsenceController extends AbstractController
{
    public function __construct(
        private readonly AbsenceService $absenceService,
        private readonly EmployeeService $employeeService,
    )
    {}


    #[Route('/create/{employeeId}', name: 'create')]
    #[Route('/edit/{employeeId}/{id}', name: 'edit')]
    public function edit(?int $id, int $employeeId, Request $request): Response
    {
        $isEdit = true;
        if(!$id){
            $isEdit=false;
            $absence = new Absence($this->employeeService->getEmployeeById($employeeId));
        }else{
            $absence = $this->absenceService->getAbsenceById($id);
        }
        $form = $this->createForm(AbsenceFormType::class, $absence);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $updatedAbsence = $form->getData();
            $this->absenceService->saveAbsence($updatedAbsence);
            if ($isEdit){
                $this->addFlash('success', 'Absence has been updated');
                return $this->redirectToRoute('app_absence_show', [
                    'employeeId' => $employeeId,
                    'id' => $id
                ]);
            }
            $this->addFlash('success', 'Absence has been created');
            return $this->redirectToRoute('app_employee_show', [
                'id' => $employeeId
            ]);
        }

        return $this->render('absence/edit.html.twig', [
            'form' => $form,
            'id' => $id,
            'employeeId' => $employeeId,
            'isEdit' => $isEdit,
        ]);
    }

    #[Route('/delete/{employeeId}/{id}', name: 'delete')]
    public function delete(int $id, int $employeeId): Response
    {
        $absence = $this->absenceService->getAbsenceById($id);
        $this->absenceService->deleteAbsence($absence);
        $this->addFlash('success', 'Absence has been deleted');
        return $this->redirectToRoute('app_employee_show', [
            'id' => $employeeId
        ]);
    }

    #[Route('/{employeeId}/{id}', name: 'show')]
    public function show(int $id, int $employeeId): Response
    {
        $absence = $this->absenceService->getAbsenceById($id);
        return $this->render("absence/show.html.twig", [
            'absence' => $absence,
            'employeeId' => $employeeId
        ]);
    }


}
