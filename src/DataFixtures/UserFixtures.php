<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\FakeUserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user->setEmail("admin@ongoing.ch");
        $user->setFirstName("Admin");
        $user->setLastName("Istrator");
        $user->setPassword("hashme");

        //$user = FakeUserFactory::createOne();

        $manager->persist($user);
        $manager->flush();
    }
}
