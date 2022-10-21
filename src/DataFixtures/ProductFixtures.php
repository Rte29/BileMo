<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;


class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 20; $i++){
            $product = new Product();
            $product->setName("Nom du téléphone n°" . $i);
            $product->setDescription("Description du téléphone n°" . $i);
            $product->setPrice(499.99);
            $manager->persist($product);

        }

        $manager->flush();
    }
}
