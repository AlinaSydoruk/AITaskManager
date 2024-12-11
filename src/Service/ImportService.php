<?php

namespace App\Service;

use App\Entity\Absence;
use App\Entity\AbsenceType;
use App\Repository\AbsenceRepository;
use App\Repository\EmployeeRepository;
use Pagerfanta\Exception\LogicException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class ImportService
{
    public function __construct(
        private EmployeeRepository      $employeeRepository,
        private AbsenceRepository       $absenceRepository,
        private ValidatorInterface      $validator,
    )
    {
    }
    public function importAbsences( string $filePath ): void
    {

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();


        /*$requiredColumns = ['Email', 'Art' , 'Kommentar' , 'Von' , 'Bis'];
        foreach ($requiredColumns as $column) {
            if (trim($worksheet->getCell($column . '1')->getValue()) !== $column) {
                throw new \LogicException("Missing or incorrect required column: $column");

            }
        }*/

        $absences = [];
        foreach ($worksheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);

            $cells = [];

            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }

            //email
            $employee = $this->employeeRepository->findEmployeeByEmail($cells[0]);
            if (!$employee) {
                throw new LogicException("Missing or incorrect email : $cells[0]");
            }

            //AbsenceType
            $absenceType = AbsenceType::tryFrom(trim($cells[1]));
            if (!$absenceType) {
                throw new \LogicException("Missing or incorrect Absence Type : $cells[1] , only ".  implode(', ', array_map(fn($case) => $case->value, AbsenceType::cases())) ."are allowed ");
            }

            $comment = $cells[2];
            $absence = new Absence($employee);

            //StartDate
            $startDate = null;

            try {
                $dateString = trim($cells[3]);
                $startDate = Date::excelToDateTimeObject($dateString);
                $absence->setStartDate($startDate);
            } catch (\Exception $e) {
                throw new \LogicException("Incorrect date type: $dateString, try dd.mm.yyyy. Error: " . $e->getMessage());
            }

            //EndDate
            $endDate = null;

            if(trim($cells[4])) {
                try {
                    $dateString = trim($cells[4]);
                    $endDate = Date::excelToDateTimeObject($dateString);
                    $absence->setEndDate($endDate);
                } catch (\Exception $e) {
                    throw new \LogicException("Incorrect date type: $dateString, try dd.mm.yyyy. Error: " . $e->getMessage());
                }
            }

            $absence->setAbsenceType($absenceType);
            $absence->setComment($comment);


            $errors = $this->validator->validate($absence);
            if (count($errors) > 0) {
                $errorsString = '';
                foreach ($errors as $violation) {
                    $errorsString = $violation->getPropertyPath() . ': ' . $violation->getMessage() . "\n";
                }
                throw new \LogicException($errorsString);
            }
            $absences[] = $absence;
        }

        foreach ($absences as $absence ){
            $this->absenceRepository->save($absence);
        }
    }
}