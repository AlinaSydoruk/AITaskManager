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

        $columnWidths = [
            'A' => 10,
            'B' => 20,
            'C' => 20,
            'D' => 25,
            'E' => 20,
            'F' => 20,
            'G' => 20
        ];
        foreach ($columnWidths as $colum => $width) {
            $employeeSheet->getColumnDimension($colum)->setWidth($width);
        }
        foreach ($employeeSheet->getColumnIterator('G', 'N') as $column){
            $employeeSheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        // headers for Employee
        $employeeSheet->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'First Name')
            ->setCellValue('C1', 'Last Name')
            ->setCellValue('D1', 'Street and Number')
            ->setCellValue('E1', 'City')
            ->setCellValue('F1', 'Job Title')
            ->setCellValue('G1', 'First Working Day')
            ->setCellValue('H1', 'Last Working Day')
            ->setCellValue('I1', 'Work Status')
            ->setCellValue('J1', 'Email')
            ->setCellValue('K1', 'Business Number')
            ->setCellValue('L1', 'Private Number')
            ->setCellValue('M1', 'Postal Code')
            ->setCellValue('N1', 'Monthly Salary')
            ->setCellValue('O1', 'Absences');


        $employees = $this->employeeRepository->findAll();
        $rowEmployee = 2;

        foreach ($employees as $employee) {
            $employeeSheet->setCellValue('A' . $rowEmployee, $employee->getId())
                ->setCellValue('B' . $rowEmployee, $employee->getFirstName())
                ->setCellValue('C' . $rowEmployee, $employee->getLastName())
                ->setCellValue('D' . $rowEmployee, $employee->getStreetAndNumber())
                ->setCellValue('E' . $rowEmployee, $employee->getCity())
                ->setCellValue('F' . $rowEmployee, $employee->getJobTitle())
                ->setCellValue('G' . $rowEmployee, $employee->getFirstWorkingDay()->format('d.m.Y'))
                ->setCellValue('H' . $rowEmployee, $employee->getLastWorkingDay() ? $employee->getLastWorkingDay()->format('d.m.Y') : $this->translator->trans('message.not_specified'))
                ->setCellValue('I' . $rowEmployee, $employee->getWorkStatus()->value)
                ->setCellValue('J' . $rowEmployee, $employee->getEmail())
                ->setCellValue('K' . $rowEmployee, $employee->getBusinessNumber())
                ->setCellValue('L' . $rowEmployee, $employee->getPrivateNumber())
                ->setCellValue('M' . $rowEmployee, $employee->getPostalCode())
                ->setCellValue('N' . $rowEmployee, 'CHF ' .  number_format($employee->getMonthlySalary(), 2, '.', "'"));


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
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP)
            ->setWrapText(true);

        $directory = $this->uploaderHelper->getPublicDownloadsPath();
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($directory ."/". $fileName);

        return $fileName;
    }
}