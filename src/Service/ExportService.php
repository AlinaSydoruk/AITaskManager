<?php

namespace App\Service;

use App\Repository\AbsenceRepository;
use App\Repository\EmployeeRepository;
use App\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExportService
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
        private readonly UploaderHelper     $uploaderHelper,
        private readonly TranslatorInterface      $translator,
    )
    {
    }
    public function exportEmployeeData(string $fileName): string
    {

        $spreadsheet = new Spreadsheet();

        $employeeSheet = $spreadsheet->getSheet(0)->setTitle('Employees');

        foreach ($employeeSheet->getColumnIterator('A', 'N') as $column){
            $employeeSheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

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


        $employees = $this->employeeRepository->findAll();
        $rowEmployee = 2;

        foreach ($employees as $employee) {
            $employeeSheet->setCellValue('A' . $rowEmployee, $employee->getId())
                ->setCellValue('B' . $rowEmployee, $employee->getFirstName())
                ->setCellValue('C' . $rowEmployee, $employee->getLastName())
                ->setCellValue('D' . $rowEmployee, $employee->getFirstWorkingDay()->format('d.m.Y'))
                ->setCellValue('E' . $rowEmployee, $employee->getLastWorkingDay() ? $employee->getLastWorkingDay()->format('d.m.Y') : $this->translator->trans('message.not_specified'))
                ->setCellValue('F' . $rowEmployee, $employee->getWorkStatus()->value)
                ->setCellValue('G' . $rowEmployee, $employee->getEmail())
                ->setCellValue('H' . $rowEmployee, $employee->getBusinessNumber())
                ->setCellValue('I' . $rowEmployee, $employee->getPrivateNumber())
                ->setCellValue('J' . $rowEmployee, $employee->getStreetAndNumber())
                ->setCellValue('K' . $rowEmployee, $employee->getCity())
                ->setCellValue('L' . $rowEmployee, $employee->getPostalCode())
                ->setCellValue('M' . $rowEmployee, 'CHF ' .  number_format($employee->getMonthlySalary(), 2, '.', "'"))
                ->setCellValue('N' . $rowEmployee, $employee->getJobTitle());

            $cellIterator = $employeeSheet->getRowIterator($rowEmployee)->current()->getCellIterator('O');
            foreach ($employee->getAbsences() as $absence) {
                $absencePeriod = $absence->getStartDate()->format('d.m.Y') . " - " . $absence->getEndDate()->format('d.m.Y');
                $cellIterator->current()->setValue($absencePeriod);
                $employeeSheet->getColumnDimension($cellIterator->current()->getColumn())->setAutoSize(true);

                $cellIterator->next();
            }

            $rowEmployee++;

        }
        $employeeSheet->getStyle($employeeSheet->calculateWorksheetDimension())
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($this->uploaderHelper->getPublicDownloadsPath() . $fileName);

        return $fileName;
    }
}