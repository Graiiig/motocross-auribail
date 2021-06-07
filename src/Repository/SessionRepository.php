<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    // /**
    //  * @return Session[] Returns an array of Session objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Session
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
    public function findNext(): ?Session
    {
    $currentDate = new \DateTime();   
    // dd($currentDate);     

        $session = $this->createQueryBuilder('s')
            ->andWhere('s.date > :date')
            ->setParameter('date', $currentDate)
            ->getQuery()
            ->setMaxResults(1)
            ->getResult()
        ;

        // dd($session);
        return $session[0];
    }

    public function findOne($id): ?Session
    {

        $session = $this->createQueryBuilder('s', 'u')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->orderBy('roles')
            ->getQuery()
            ->setMaxResults(1)
            ->getResult()
        ;

        return $session[0];
    }

    

    
    public function findNextAll(): ?array
    {
    $currentDate = new \DateTime();   
    // dd($currentDate);     

        $session = $this->createQueryBuilder('s')
            ->andWhere('s.date > :date')
            ->setParameter('date', $currentDate)
            ->getQuery()
            ->getResult()
        ;

        // dd($session);
        return $session;
    }
    
}
