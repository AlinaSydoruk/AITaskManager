<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeFormType;
use App\Repository\EmployeeRepository;
use App\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/employee', name: 'app_employee_')]
class EmployeeController extends AbstractController
{
    public function __construct(
        private readonly EmployeeService $employeeService
    )
    {}

    #[Route('/', name: 'index')]
    public function index(EmployeeRepository $employeeRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $pagerfanta = $this->employeeService->paginateEmployees($page,10);
        return $this->render('employee/index.html.twig',[
            'pagerfanta' => $pagerfanta,
        ]);
    }


    #[Route('/{id}', name: 'show')]
    public function show(Employee $employee): Response
    {
        return $this->render("employee/show.html.twig", [
            'employee' => $employee,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit (Employee $employee , Request $request , ): Response
    {
        $form = $this->createForm(EmployeeFormType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $updatedEmployee = $form->getData();
            $this->employeeService->updateEmployee($updatedEmployee);
            $this->addFlash('success', 'Employee has been updated');
            return $this->redirectToRoute('app_employee_show', [
                'id' => $employee->getId()
            ]);
        }

        return $this->render('employee/edit.html.twig', [
            'form' => $form,
            'id' => $employee->getId(),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete')]
    public function delete(Employee $employee): Response
    {
        $this->employeeService->deleteEmployee($employee);
        $this->addFlash('success', 'Employee has been deleted');
        return $this->redirectToRoute('app_employee_index');
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request): Response
    {
        dd($request);
        $form = $this->createForm(EmployeeFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $employee = $form->getData();
            dd($employee);
            $this->employeeService->createEmployee($employee);
            $this->addFlash('success', 'Employee has been created');
            return $this->redirectToRoute('app_employee_index');
        }

        return $this->render('employee/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
