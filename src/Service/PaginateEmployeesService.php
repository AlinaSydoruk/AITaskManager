<?php

namespace App\Service;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

readonly class PaginateEmployeesService
{
    public function __construct(
        private EmployeeRepository        $employeeRepository
    ) { }


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
}