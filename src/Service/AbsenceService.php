<?php

namespace App\Service;

use App\Entity\Absence;
use App\Entity\Employee;
use App\Repository\AbsenceRepository;
use App\Repository\EmployeeRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

readonly class AbsenceService
{
    public function __construct(
        private AbsenceRepository        $absenceRepository
    ) { }


    public function deleteAbsence(Absence $absence): void
    {
        $this->absenceRepository->remove($absence);
    }

    public function saveAbsence(Absence $absence): void
    {
        $this->absenceRepository->save($absence);
    }

    public function getAbsenceById(int $id): Absence
    {
        return $this->absenceRepository->find($id);
    }
}