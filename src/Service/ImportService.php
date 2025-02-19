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
use PhpOffice\PhpSpreadsheet\Worksheet\CellIterator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ImportService
{
    public function __construct(
        private EmployeeRepository  $employeeRepository,
        private AbsenceRepository   $absenceRepository,
        private ValidatorInterface  $validator,
        private TranslatorInterface $translator,
        private VocationService     $vocationService,
    )
    {
    }

    public function importAbsences(string $filePath): void
    {

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();


        $absences = [];
        foreach ($worksheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();

            $cells = [];

            if (!$row->isEmpty(definitionOfEmptyFlags: CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL)) {
                foreach ($cellIterator as $cell) {
                    $cells[] = trim($cell->getValue());
                }
                $absence = null;

                // id
                $id = $cells[0];
                if ($id) {
                    $absence = $this->absenceRepository->find($id);
                    $absence ?: throw new \LogicException($this->translator->trans("error.incorrect_id_in_row") . $row->getRowIndex());
                } else {
                    throw new \LogicException($this->translator->trans("error.missed_id_in_row") . $row->getRowIndex());
                }

                //email
                if (trim(!$cells[1])) {
                    throw new \LogicException($this->translator->trans("error.missed_email_in_row") . $row->getRowIndex());
                }
                $employee = $this->employeeRepository->findEmployeeByEmail($cells[1]);
                if (!$employee) {
                    throw new LogicException($this->translator->trans("error.unable_to_find_employee_with_email",['email' => $cells[1]]));
                }

                if (!$absence) {
                    $absence = new Absence($employee);
                } else {
                    $absence->setEmployee($employee);
                }

                $type = trim($cells[2]);
                if (trim(!$type)) {
                    throw new \LogicException($this->translator->trans("error.missed_absence_type_in_row") . $row->getRowIndex());
                }
                $absenceType = AbsenceType::tryFrom($type);

                if (!$absenceType) {
                    throw new \LogicException($this->translator->trans("error.incorrect_absence_type", ['absenceType' => $cells[2] , 'allowedTypes' =>implode(', ', array_map(fn($case) => $case->value, AbsenceType::cases()))]));
                }
                $absence->setAbsenceType($absenceType);

                //Comment
                $comment = $cells[3];
                $absence->setComment($comment);

                //StartDate
                $startDate = null;
                $date = trim($cells[4]);
                if (!$date) {
                    throw new \LogicException($this->translator->trans("error.missed_start_date_in_row") . $row->getRowIndex());
                }
                if (is_numeric($date)) {
                    $startDate = Date::excelToDateTimeObject(intval($date));
                    $absence->setStartDate($startDate);
                } else if ($startDate = DateTime::createFromFormat('d.m.Y', $date)) {
                    $absence->setStartDate($startDate);
                } else throw new \LogicException($this->translator->trans('error.incorrect_date_type', ['date' => $date]));


                //EndDate
                $endDate = null;
                $date = $cells[5];
                if (!$date) {
                    throw new \LogicException($this->translator->trans("error.missed_end_date_in_row") . $row->getRowIndex());
                }
                if (is_numeric($date)) {
                    $endDate = Date::excelToDateTimeObject(intval($date));
                    $absence->setEndDate($endDate);
                } else if ($endDate = DateTime::createFromFormat('d.m.Y', $date)) {
                    $absence->setEndDate($endDate);
                } else throw new \LogicException($this->translator->trans('error.incorrect_date_type', ['date' => $date]));

                $errors = $this->validator->validate($absence);
                if (count($errors) > 0) {
                    $errorsString = '';
                    foreach ($errors as $violation) {
                        $errorsString = $violation->getPropertyPath() . ': ' . $violation->getMessage() . "\n";
                    }
                    throw new \LogicException($this->translator->trans('error.absences_not_valid_for_employee') . $absence->getEmployee()->getEmail() . ' ' . $errorsString);
                }
                $absences[] = $absence;
            }
        }

        foreach ($absences as $absence) {
            $this->absenceRepository->save($absence);
        }
    }
}