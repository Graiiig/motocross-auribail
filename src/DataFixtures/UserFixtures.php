<?php

namespace App\DataFixtures;

use App\Entity\Session;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    
     private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }
    
    public function load(ObjectManager $manager)
    {
        
        $faker = Faker\Factory::create('fr_FR');
        $this->manager = $manager;



        $session = new Session();
        $session->setTitle('La coupe du travail')
             ->setDate(new \DateTime('2021-05-02') );
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
                 ->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    '123'
                ))
                 ->setRoles(['ROLE_MEMBER'])
                 ->setBirthdate( new \DateTime('200'.$i.'-06-06'))
                 ->setLicense($faker->randomNumber($nbDigits = NULL, $strict = false));

                if ($i<20) {
                    $user->addSession($session);
                }
                else if ($i>=20 && $i < 30) {
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
                 ->setPassword($this->passwordEncoder->encodePassword(
                                 $user,
                                 '123'
                             ))
                 ->setRoles(['ROLE_NON_MEMBER'])
                 ->setBirthdate( new \DateTime('200'.$i.'-06-06'))
                 ->setLicense($faker->randomNumber($nbDigits = NULL, $strict = false));

                 if ($i<20) {
                    $user->addSession($session);
                }
                else if ($i>=20 && $i < 30) {
                    $user->addSession($session2);

                }
            $manager->persist($user);
        }




        $user = new User();
        $user->setLastName('Pita')
             ->setFirstName('Patrick')
             ->setEmail('pat@pita.fr')
             ->setPhoneNumber('$$$$$$$')
             ->setAddress("2 rue des escrocs")
             ->setPassword($this->passwordEncoder->encodePassword(
                $user,
                '123'
            ))
             ->setRoles(['ROLE_ADMIN'])
             ->setBirthdate( new \DateTime('2009-06-06'))
             ->setLicense('$$$$$$');
            
            
        $manager->persist($user);


        $manager->flush();
    }
}
