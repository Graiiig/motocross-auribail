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

  
    //  * @return Session[] Returns an array of Session objects
    //  */
   

    //Fonction pour trouver la prochaine session
    //On récupère la première session qui a une date supérieure à la date actuelle
    public function findNext(): ?Session
    {
    $currentDate = new \DateTime();
        
    $session = $this->createQueryBuilder('s')
            ->andWhere('s.date > :date')
            ->setParameter('date', $currentDate)
            ->orderBy('s.date', 'ASC')
            ->getQuery()
            ->setMaxResults(1)
            ->getResult()
        ;

        return $session[0];
    }
}
