<?php

namespace App\DataFixtures;


use App\Factory\BoardFactory;
use App\Factory\TaskFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BoardFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        BoardFactory::createMany(5);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
