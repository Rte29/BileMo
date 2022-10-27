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
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
        $user->setName("user");
        $manager->persist($user);

        $listUser[] = $user;

        //création d'un "admin"
        $admin = new User();
        $admin->setEmail("admin@bilemoapi.com");
        $admin->setRoles(["ROLE_SUPER_ADMIN"]);
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, "password"));
        $admin->setName("admin");
        $manager->persist($admin);

        $listUser[] = $user;

        //création d'un "customer"
        $customer = new User();
        $customer->setEmail("customer@bilemoapi.com");
        $customer->setRoles(["ROLE_USER"]);
        $customer->setPassword($this->userPasswordHasher->hashPassword($customer, "password"));
        $customer->setName("customer");
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
