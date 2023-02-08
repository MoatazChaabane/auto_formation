<?php

namespace App\DataFixtures;

use App\Entity\Hobby;
use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HobbyFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data=['yoga','cuisine','blogging','lecture','langues','phtographie'];
        for($i=0;$i<count($data);$i++){
            $hobby = new Hobby();
            $hobby->setDesignation($data[$i]);
            $manager->persist($hobby);
        }
        $manager->flush();
    }
}
