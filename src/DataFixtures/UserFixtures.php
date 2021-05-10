<?php

namespace App\DataFixtures;

use App\Entity\Session;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
class UserFixtures extends Fixture
{
    
    
    public function load(ObjectManager $manager)
    {
        
        $faker = Faker\Factory::create('fr_FR');
        $this->manager = $manager;



        $session = new Session();
        $session->setTitle('La coupe du débarquement')
             ->setDate(new \DateTime('2021-06-06') );
        $manager->persist($session);

        $session2 = new Session();
        $session2->setTitle('La coupe de champagne')
             ->setDate(new \DateTime('2021-12-05') );
        $manager->persist($session2);

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

                if ($i<13) {
                    $user->addSession($session);
                }
                else if ($i>=13 && $i < 30) {
                    $user->addSession($session2);

                }
                
            $manager->persist($user);
        }
        for ($i=0; $i < 40 ; $i++) { 
            $user = new User();
            $user->setLastName($faker->lastName)
                 ->setFirstName($faker->firstName)
                 ->setEmail($faker->email)
                 ->setPhoneNumber($faker->phoneNumber)
                 ->setAddress("2 rue du coquinou")
                 ->setPassword('123') 
                 ->setRoles(['ROLE_NON_MEMBER'])
                 ->setBirthdate( new \DateTime())
                 ->setLicence($faker->randomNumber($nbDigits = NULL, $strict = false));

                 if ($i<13) {
                    $user->addSession($session);
                }
                else if ($i>=13 && $i < 30) {
                    $user->addSession($session2);

                }
            $manager->persist($user);
        }



        $manager->flush();
    }
}
