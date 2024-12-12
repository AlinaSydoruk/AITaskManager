<?php

namespace App\Service;

use App\Entity\Absence;
use App\Entity\AbsenceType;
use App\Repository\AbsenceRepository;
use App\Repository\EmployeeRepository;
use DateTime;
use Gedmo\Translator\TranslationInterface;
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

            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }

            //email
            $employee = $this->employeeRepository->findEmployeeByEmail($cells[0]);

            if (!$employee) {
                throw new LogicException("Unable to find employee with email : $cells[0]");
            }

            //AbsenceType
             // for future
            // $type =  $this->translator->trans('absence.type_'. strtolower( str_replace( ' ', '_' ,$cells[1])));




            $absenceType = AbsenceType::tryFrom(trim($cells[1]));

            if (!$absenceType) {
                throw new \LogicException("Missing or incorrect Absence Type : $cells[1] , only ".  implode(', ', array_map(fn($case) => $case->value, AbsenceType::cases())) ."are allowed ");
            }

            $comment = $cells[2];
            $absence = new Absence($employee);

            //StartDate
            $startDate = null;

            $date = ($cells[3]);
            if (is_int($date) && Date::isDateTimeFormat($date)) {
                $startDate = Date::excelToDateTimeObject($date);
                $absence->setStartDate($startDate);
            } else if($startDate = DateTime::createFromFormat('d.m.Y', $date)){
                    $absence->setStartDate($startDate);
            } else throw new \LogicException("Incorrect date type: $date, try dd.mm.yyyy.  " );


            //EndDate
            $endDate = null;

            $dateString = trim($cells[4]);
            if (is_int($date) && Date::isDateTimeFormat($dateString)) {
                $endDate = Date::excelToDateTimeObject($dateString);
                $absence->setEndDate($endDate);
            } else
                try {
                    $endDate = DateTime::createFromFormat('d.m.Y', $dateString);
                    $absence->setEndDate($endDate);
                } catch (\Exception $e) {
                    throw new \LogicException("Incorrect date type: $dateString, try dd.mm.yyyy. Error: " . $e->getMessage());
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