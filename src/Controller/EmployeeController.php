<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeFormType;
use App\Repository\EmployeeRepository;
use App\Service\PaginateEmployeesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/employee', name: 'app_employee_')]
class EmployeeController extends AbstractController
{
    public function __construct(
        private readonly PaginateEmployeesService $paginateEmployeesService,
        private readonly EmployeeRepository       $employeeRepository
    )
    {}

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $pagerfanta = $this->paginateEmployeesService->paginateEmployees($page,10);
        return $this->render('employee/index.html.twig',[
            'pagerfanta' => $pagerfanta,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeFormType::class, $employee);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $employee = $form->getData();
            $this->employeeRepository->save($employee);
            $this->addFlash('success', 'Employee has been created');
            return $this->redirectToRoute('app_employee_index');
        }

        return $this->render('employee/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(Employee $employee): Response
    {
        return $this->render("employee/show.html.twig", [
            'employee' => $employee,
        ]);
    }

   #[Route('/delete/{id}', name: 'delete')]
    public function delete(Employee $employee): Response
    {
        $this->employeeRepository->remove($employee);
        $this->addFlash('success', 'Employee has been deleted');
        return $this->redirectToRoute('app_employee_index');
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(int $id , Request $request): Response
    {
        $employee = $this->employeeRepository->find($id);
        $form = $this->createForm(EmployeeFormType::class, $employee);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $updatedEmployee = $form->getData();
            $this->employeeRepository->save($updatedEmployee);
            $this->addFlash('success', 'Employee has been updated');
            return $this->redirectToRoute('app_employee_show', [
                'id' => $id
            ]);
        }

        return $this->render('employee/edit.html.twig', [
            'form' => $form,
            'id' => $employee->getId(),
        ]);
    }

}
