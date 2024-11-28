<?php

namespace App\Service;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

readonly class EmployeeService
{
    public function __construct(
        private EmployeeRepository        $employeeRepository
    ) { }


    public function updateEmployee(Employee $employee): void
    {
        $this->employeeRepository->save($employee);
    }

    public function paginateEmployees($page, $maxEmployeesPerPage): Pagerfanta
    {
        $queryBuilder = $this->employeeRepository->createEmployeesQueryBuilder();
        $pagerfanta = new Pagerfanta(
            new QueryAdapter($queryBuilder)
        );
        $pagerfanta->setMaxPerPage($maxEmployeesPerPage);
        $pagerfanta->setCurrentPage($page);
        return $pagerfanta;
    }

    public function deleteEmployee(Employee $employee): void
    {
        $this->employeeRepository->remove($employee);
    }

    public function createEmployee(Employee $employee): void
    {
        $this->employeeRepository->save($employee);
    }
}