<?php

namespace App\DataFixtures;


use App\Entity\Board;
use App\Factory\TaskFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        TaskFactory::createMany(20);
    }

    public function getDependencies(): array
    {
        return [
            BoardFixtures::class,
        ];
    }
}
