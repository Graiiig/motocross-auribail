<?php

namespace App\DataFixtures;

use App\Entity\PendingList;
use App\Entity\Session;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PendingListFixtures extends Fixture
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

        for ($i = 0; $i < 15; $i++) {

            $user = new User();
            $user->setLastName($faker->lastName)
                ->setFirstName($faker->firstName)
                ->setEmail($faker->email)
                ->setPhoneNumber($faker->phoneNumber)
                ->setAddress($faker->streetAddress)
                ->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    '123'
                ))
                ->setRoles(['ROLE_MEMBER'])
                ->setBirthdate($faker->dateTime('Y-m-d', '-20 years'))
                ->setLicense($faker->randomNumber($nbDigits = NULL, $strict = false));

            $session = new Session();
            $session->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true));
            $session->setDate($faker->dateTimeBetween($startDate = 'now', $endDate = '+5 years'));
            $session->setStatus(rand(0, 1));

            $pendingList = new PendingList();
            $pendingList->setSession($session);
            $pendingList->setUser($user);
            $pendingList->setDatetime(new \DateTime());

            $manager->persist($pendingList);
            $manager->flush();
        }
    }
}
