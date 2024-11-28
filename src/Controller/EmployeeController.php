<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeFormType;
use App\Repository\EmployeeRepository;
use App\Service\EmployeeService;
use BabDev\PagerfantaBundle\Serializer\Handler\PagerfantaHandler;

use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
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
        $queryBuilder = $employeeRepository->createEmployeesQueryBuilder();
        $pagerfanta = new Pagerfanta(
            new QueryAdapter($queryBuilder)
        );
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));
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

            return $this->redirectToRoute('app_employee_show', [
                'id' => $employee->getId()
            ]);
        }

        return $this->render('employee/edit.html.twig', [
            'form' => $form,
            'id' => $employee->getId(),
        ]);
    }

}
