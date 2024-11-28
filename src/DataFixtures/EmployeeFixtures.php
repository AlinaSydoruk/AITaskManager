<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\User;
use App\Entity\WorkStatus;
use App\Factory\FakeUserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EmployeeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $employee = new Employee();

        $employee->setFirstName("Alan");
        $employee->setLastName("Lipton");
        $employee->setFirstWorkingDay(new \DateTime('now'));
        $employee->setWorkStatus(WorkStatus::notYetStartedWorking);
        $employee->setEmail('alanlipton@ongoing.ch');
        $employee->setBusinessNumber('123456789');
        $employee->setPrivateNumber('123456789');
        $employee->setStreetAndNumber('Bakery Street 666');
        $employee->setCity('New York');
        $employee->setPostalCode('1111');
        $employee->setMonthlySalary(5000);
        $employee->setJobTitle('Manager');

        $manager->persist($employee);
        $manager->flush();
    }
}
