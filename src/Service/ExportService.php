<?php

namespace App\Service;

use App\Repository\AbsenceRepository;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportService
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
    )
    {
    }


    public function exportEmployeeData(): string
    {

        $spreadsheet = new Spreadsheet();

        $employeeSheet = $spreadsheet->createSheet(0);
        $employeeSheet->setTitle('Employees');

        $absenceSheet = $spreadsheet->createSheet(1);
        $absenceSheet->setTitle('Absences');

        // headers for Employee
        $employeeSheet->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'First Name')
            ->setCellValue('C1', 'Last Name')
            ->setCellValue('D1', 'First Working Day')
            ->setCellValue('E1', 'Last Working Day')
            ->setCellValue('F1', 'Work Status')
            ->setCellValue('G1', 'Email')
            ->setCellValue('H1', 'Business Number')
            ->setCellValue('I1', 'Private Number')
            ->setCellValue('J1', 'Street and Number')
            ->setCellValue('K1', 'City')
            ->setCellValue('L1', 'Postal Code')
            ->setCellValue('M1', 'Monthly Salary')
            ->setCellValue('N1', 'Job Title')
            ->setCellValue('O1', 'Absences');

        // headers for Absence
        $absenceSheet->setCellValue('A1', 'Absence ID')
            ->setCellValue('B1', 'Employee ID')
            ->setCellValue('C1', 'Start Date')
            ->setCellValue('D1', 'End Date')
            ->setCellValue('E1', 'Absence Type')
            ->setCellValue('F1', 'Comment');

        $employees = $this->employeeRepository->findAll();
        $rowAbsence = 2;
        $rowEmployee = 2;

        foreach ($employees as $employee) {
            $employeeSheet->setCellValue('A' . $rowEmployee, $employee->getId())
                ->setCellValue('B' . $rowEmployee, $employee->getFirstName())
                ->setCellValue('C' . $rowEmployee, $employee->getLastName())
                ->setCellValue('D' . $rowEmployee, $employee->getFirstWorkingDay()->format('Y-m-d'))
                ->setCellValue('E' . $rowEmployee, $employee->getLastWorkingDay() ? $employee->getLastWorkingDay()->format('Y-m-d') : '')
                ->setCellValue('F' . $rowEmployee, $employee->getWorkStatus()->value)
                ->setCellValue('G' . $rowEmployee, $employee->getEmail())
                ->setCellValue('H' . $rowEmployee, $employee->getBusinessNumber())
                ->setCellValue('I' . $rowEmployee, $employee->getPrivateNumber())
                ->setCellValue('J' . $rowEmployee, $employee->getStreetAndNumber())
                ->setCellValue('K' . $rowEmployee, $employee->getCity())
                ->setCellValue('L' . $rowEmployee, $employee->getPostalCode())
                ->setCellValue('M' . $rowEmployee, $employee->getMonthlySalary())
                ->setCellValue('N' . $rowEmployee, $employee->getJobTitle());


            $absenceIds=[];
            foreach ($employee->getAbsences() as $absence) {
                $absenceIds[] = $absence->getId();
                $absenceSheet->setCellValue('A' . $rowAbsence, $absence->getId())
                    ->setCellValue('B' . $rowAbsence, $employee->getId())
                    ->setCellValue('C' . $rowAbsence, $absence->getStartDate()->format('Y-m-d'))
                    ->setCellValue('D' . $rowAbsence, $absence->getEndDate() ? $absence->getEndDate()->format('Y-m-d') : '')
                    ->setCellValue('E' . $rowAbsence, $absence->getAbsenceType()->value)
                    ->setCellValue('F' . $rowAbsence, $absence->getComment());

                $rowAbsence++;
            }
            $employeeSheet->setCellValue('O' . $rowEmployee, implode(', ', $absenceIds));
            $rowEmployee++;
            $rowAbsence++;
        }

        $fileName = 'employee.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);

        return $fileName;
    }


   /* public function exportAbsenceData(): string
    {


    }*/
}