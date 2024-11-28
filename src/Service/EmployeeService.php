<?php

namespace App\Service;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;

readonly class EmployeeService
{
    public function __construct(
        private EmployeeRepository        $employeeRepository
    ) { }


    public function updateEmployee(Employee $employee): void
    {
        $this->employeeRepository->update($employee);
    }
}