<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user->setEmail("user@gmail.com");
        $user->setFirstName("User");
        $user->setLastName("User");
        $user->setPassword('$2y$13$2FvncABPZ1/FGwR54O7vVe1DhWz5krL.Se9ge5kPlyFeHuelM0qHK');
        $user->setRoles( ['ROLE_USER']);

        $user1 = new User();
        $user1->setEmail("user1@example.com");
        $user1->setRoles(['ROLE_USER']);
        $user1->setFirstName("Alan");
        $user1->setLastName("Lipton");
        $user1->setPassword(
            $this->passwordHasher->hashPassword($user1, 'test')
        );

        $user2 = new User();
        $user2->setEmail("user2@example.com");
        $user2->setRoles(['ROLE_USER']);
        $user2->setFirstName("Bob");
        $user2->setLastName("Smith");
        $user2->setPassword(
            $this->passwordHasher->hashPassword($user2, 'test')
        );



        //$user = FakeUserFactory::createOne();
        $manager->persist($user1);
        $manager->persist($user2);
        $manager->persist($user);
        $manager->flush();

        UserFactory::createMany(5);
    }
}
