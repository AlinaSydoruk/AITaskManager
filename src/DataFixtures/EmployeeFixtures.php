<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\User;
use App\Entity\WorkStatus;
use App\Factory\FakeUserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Random;

class EmployeeFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $employees = [
            ['admin', 'admin', 'admin@ongoing.ch'],
            ['Alan', 'Lipton', 'alan.lipton@ongoing.ch'],
            ['Max', 'Muster', 'max.muster@ongoing.ch'],
            ['Moritz', 'Muster', 'moritz.muster@ongoing.ch'],
            ['Peter', 'Muster', 'peter.muster@ongoing.ch'],
            ['Meister', 'Hans', 'meister.hans@ongoing.ch'],
            ['Fred', 'Fertig', 'fred.fertig@ongoing.ch'],
            ['Anna', 'Muster', 'anna.muster@ongoing.ch'],
            ['Bettina', 'Muster', 'bettina.muster@ongoing.ch'],
            ['Claudia', 'Muster', 'claudia.muster@ongoing.ch']
        ];

        foreach ($employees as $data) {
            $employee = new Employee();

            $employee->setFirstName($data[0]);
            $employee->setLastName($data[1]);
            $employee->setFirstWorkingDay(new \DateTime('now'));
            $employee->setWorkStatus(WorkStatus::notYetStartedWorking);
            $employee->setEmail($data[2]);
            $employee->setBusinessNumber('123456789');
            $employee->setPrivateNumber('987654321');
            $employee->setStreetAndNumber('Example Street 123');
            $employee->setCity('Example City');
            $employee->setPostalCode('12345');
            $employee->setMonthlySalary(Random::randBetween(4500, 9000));
            $employee->setJobTitle('Employee');

            $manager->persist($employee);
        }

        $manager->flush();
    }
}
