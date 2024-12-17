<?php

namespace App\Service;

use App\Entity\Absence;
use App\Entity\AbsenceType;
use App\Repository\AbsenceRepository;
use App\Repository\EmployeeRepository;
use DateTime;
use Pagerfanta\Exception\LogicException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ImportService
{
    public function __construct(
        private EmployeeRepository      $employeeRepository,
        private AbsenceRepository       $absenceRepository,
        private ValidatorInterface      $validator,
        private TranslatorInterface     $translator,
    )
    {
    }
    public function importAbsences( string $filePath ): void
    {

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();


        $absences = [];
        foreach ($worksheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);
            $cells = [];
            if(!$row->isEmpty()) {
                foreach ($cellIterator as $cell) {
                    $cells[] = $cell->getValue();
                }
                if (sizeof($cells) < 5) {
                    throw new \LogicException("where are empty cells in row " . $row->getRowIndex());
                }

                    //email
                    $employee = $this->employeeRepository->findEmployeeByEmail($cells[0]);

                    if (!$employee) {
                        throw new LogicException("Unable to find employee with email : $cells[0]");
                    }

                    $absenceType = AbsenceType::tryFrom(trim($cells[1]));

                    if (!$absenceType) {
                        throw new \LogicException("Missing or incorrect Absence Type : $cells[1] , only " . implode(', ', array_map(fn($case) => $case->value, AbsenceType::cases())) . "are allowed ");
                    }

                    $comment = $cells[2];
                    $absence = new Absence($employee);

                    //StartDate
                    $startDate = null;

                    $date = trim($cells[3]);
                    if (is_numeric($date)) {
                        $startDate = Date::excelToDateTimeObject(intval($date));
                        $absence->setStartDate($startDate);
                    } else if ($startDate = DateTime::createFromFormat('d.m.Y', $date)) {
                        $absence->setStartDate($startDate);
                    } else throw new \LogicException("Incorrect date type: $date, try dd.mm.yyyy.  ");


                    //EndDate
                    $endDate = null;
                    $date = trim($cells[4]);
                    if (is_numeric($date)) {
                        $endDate = Date::excelToDateTimeObject(intval($date));
                        $absence->setEndDate($endDate);
                    } else if ($endDate = DateTime::createFromFormat('d.m.Y', $date)) {
                        $absence->setEndDate($endDate);
                    } else throw new \LogicException("Incorrect date type: $date, try dd.mm.yyyy.  ");


                    $absence->setAbsenceType($absenceType);
                    $absence->setComment($comment);


                    $errors = $this->validator->validate($absence);
                    if (count($errors) > 0) {
                        $errorsString = '';
                        foreach ($errors as $violation) {
                            $errorsString = $violation->getPropertyPath() . ': ' . $violation->getMessage() . "\n";
                        }
                        throw new \LogicException(' Absences are not valid for this employee ' . $absence->getEmployee()->getEmail() . ' ' . $errorsString);
                    }
                    $absences[] = $absence;
                }
            }

        foreach ($absences as $absence ){
            $this->absenceRepository->save($absence);
        }
    }
}