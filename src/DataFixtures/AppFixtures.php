<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 1; $i < 20; $i++) {
            $product = new Product();
            $product->setName("Nom du téléphone n°" . $i);
            $product->setDescription("Description du téléphone n°" . $i);
            $product->setPrice($faker->numberBetween(100, 800));
            $manager->persist($product);
        }

        //création des customers
        $listUser = [];

        //création d'un user "normal"
        $user = new User();
        $user->setEmail("user@bilemoapi.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
        $user->setName("user");
        $manager->persist($user);

        $listUser[] = $user;

        //création d'un user "admin"
        $Admin = new User();
        $Admin->setEmail("admin@bilemoapi.com");
        $Admin->setRoles(["ROLE_ADMIN"]);
        $Admin->setPassword($this->userPasswordHasher->hashPassword($Admin, "password"));
        $Admin->setName("user");
        $manager->persist($Admin);

        $listUser[] = $user;

        //création d'un user "externe"
        $customer = new User();
        $customer->setEmail("externe@bilemoapi.com");
        $customer->setRoles(["ROLE_CUSTOMER"]);
        $customer->setPassword($this->userPasswordHasher->hashPassword($customer, "password"));
        $customer->setName("externe");
        $manager->persist($customer);

        //on sauvegarde le customer créé dans un tableau
        $listUser[] = $user;

        for ($i = 1; $i < 20; $i++) {
            $customer = new Customer();
            $customer->setLastName($faker->lastname);
            $customer->setFirstName($faker->firstName);
            $customer->setEmail($faker->email);
            $customer->setRelation($listUser[array_rand($listUser)]);
            $manager->persist($customer);
        }


        $manager->flush();
    }
}
