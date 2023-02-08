<?php

namespace App\DataFixtures;

use App\Entity\Personne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PersonneFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('ffr_FR');

        for ($i=0;$i<100;$i++){
            $personne=new Personne();
            $personne->setFisrtname($faker->firstName);
            $personne->setName($faker->name);
            $personne->setAge($faker->numberBetween(18,60));
            $manager->persist($personne);

        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
