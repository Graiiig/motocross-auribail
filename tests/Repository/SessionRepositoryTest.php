<?php

namespace App\Test\Repository;

use App\Entity\Session;
use App\DataFixtures\SessionFixtures;
use App\Repository\SessionRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SessionRepositoryTest extends KernelTestCase
{
    use FixturesTrait;
    
    public function testFindNext()
    {
        $currentDate = new \DateTime();
        $currentDate->modify("-1 minutes");

        self::bootKernel();
        $this->loadFixtures([SessionFixtures::class]);

        $sessions = self::$container->get(SessionRepository::class)->findAll();

        $nextSesssion = self::$container->get(SessionRepository::class)->findNext();
        
        foreach ($sessions as $session) {
            $this->assertGreaterThanOrEqual($nextSesssion->getDate(), $session->getDate());
        }
    }
}
