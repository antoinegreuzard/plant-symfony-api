<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('antoine@example.com');
        $user->setUsername('antoine.greuzard');
        $user->setRoles(['ROLE_USER']);

        $hashedPassword = $this->hasher->hashPassword($user, 'antoine');
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();

        $this->addReference('antoine_user', $user);
    }
}
