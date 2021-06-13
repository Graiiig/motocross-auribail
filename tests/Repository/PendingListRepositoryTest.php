<?php

namespace App\Test\Repository;

use App\Entity\PendingList;
use App\DataFixtures\PendingListFixtures;
use App\Repository\PendingListRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PendingListRepositoryTest extends KernelTestCase
{
    use FixturesTrait;
    
    public function testFindMembers()
    {
        self::bootKernel();
        $this->loadFixtures([PendingListFixtures::class]);
        
        $pendingLists = self::$container->get(PendingListRepository::class)->findAll();

        foreach($pendingLists as $pendingList){
            $pendingList->getId();
        }

    }
}
