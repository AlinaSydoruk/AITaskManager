<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\Holiday;
use App\Entity\User;
use App\Entity\WorkStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Random;

class HolidaysFixtures extends Fixture
{

    /**
     * @throws \DateMalformedStringException
     */
    public function load(ObjectManager $manager): void
    {
        $holidays = [
            ['2025-01-01', 'Neujahr'],
            ['2025-05-29', 'Christi Himmelfahrt'],
            ['2025-08-01', 'Bundesfeiertag'],
            ['2025-09-21', 'Eidgenössischer Dank-, Buss- und Bettag'],
            ['2025-12-25', 'Erster Weihnachtstag']
        ];

        foreach ($holidays as $data) {
            $holiday = new Holiday();
            $holiday->setDate(new \DateTime($data[0]));
            $holiday->setTitle($data[1]);
            $holiday->setCanton(null);

            $manager->persist($holiday);
        }

        $manager->flush();
    }
}
