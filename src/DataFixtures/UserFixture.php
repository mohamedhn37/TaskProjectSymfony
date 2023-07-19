<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private $passwordEncoder;
    public function __construct(UserPasswordHasherInterface $passwordEncoder){
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $encodedPassword = $this->passwordEncoder->hashPassword($user, "moha123");
        $user->setUsername("mohamed");
        $user->setEmail("mohamed.hannaoui@gmail.com");
        $user->setPassword($encodedPassword);
        $manager->persist($user);
        $manager->flush();
    }
}
