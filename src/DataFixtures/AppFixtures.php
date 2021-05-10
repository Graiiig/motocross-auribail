<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
class AppFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {

        $faker = Faker\Factory::create('fr_FR');
        $this->manager = $manager;
        
        for ($i=0; $i < 40 ; $i++) { 
            $user = new User();
            $user->setLastName($faker->lastName)
                 ->setFirstName($faker->firstName)
                 ->setEmail($faker->email)
                 ->setPhoneNumber($faker->phoneNumber)
                 ->setAddress("2 rue du coquinou")
                 ->setPassword('123') 
                 ->setRoles(['ROLE_MEMBER'])
                 ->setBirthdate( new \DateTime())
                 ->setLicence($faker->randomNumber($nbDigits = NULL, $strict = false));

            # code...
            $manager->persist($user);
        }


        $manager->flush();
    }
}
