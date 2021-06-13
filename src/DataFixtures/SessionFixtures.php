<?php

namespace App\DataFixtures;

use App\Entity\PendingList;
use App\Entity\Session;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SessionFixtures extends Fixture
{
    
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $this->manager = $manager;

        for ($i=0; $i < 15 ; $i++) { 
            $session = new Session();
            $session->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true));
            $session->setDate($faker->dateTimeBetween($startDate = 'now', $endDate = '+5 years'));
            $session->setStatus(rand(0,1));

            $manager->persist($session);
            $manager->flush();
        }
    }
}
