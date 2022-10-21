<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        //création d'un user "normal"
        $user = new User();
        $user->setEmail("user@bilemoapi.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
        $user->setName("user");
        $manager->persist($user);

        //création d'un user "admin"
        $userAdmin = new User();
        $userAdmin->setEmail("admin@bilemoapi.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $userAdmin->setName("user");
        $manager->persist($userAdmin);

        //création d'un user "externe"
        $userExterne = new User();
        $userExterne->setEmail("externe@bilemoapi.com");
        $userExterne->setRoles(["ROLE_USER"]);
        $userExterne->setPassword($this->userPasswordHasher->hashPassword($userExterne, "password"));
        $userExterne->setName("externe");
        $manager->persist($userExterne);



        $manager->flush();
    }
}
