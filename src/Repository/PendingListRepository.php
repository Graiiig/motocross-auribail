<?php

namespace App\Repository;

use App\Entity\PendingList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PendingList|null find($id, $lockMode = null, $lockVersion = null)
 * @method PendingList|null findOneBy(array $criteria, array $orderBy = null)
 * @method PendingList[]    findAll()
 * @method PendingList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PendingListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PendingList::class);
    }

    // /**
    //  * @return PendingList[] Returns an array of PendingList objects
    //  */
    
    public function findMembers($sessionId, $role, $operator, $age)
    {


        $qb = $this->getEntityManager()->createQueryBuilder();

        return 
            $qb->select('pl')
            ->from('App\Entity\PendingList', 'pl')
            ->innerJoin('App\Entity\User', 'u', 'WITH', 'u.id = pl.user')
            ->andWhere("DATE_DIFF(CURRENT_DATE(), u.birthdate) >= 50")
            ->andWhere('u.roles = :role')
            ->andWhere('pl.session = :sessionId')
            ->andWhere('TIMESTAMPDIFF(YEAR,u.birthdate,CURRENT_DATE()) '.$operator.' :age')
            ->setParameter('role', $role)
            ->setParameter('sessionId', $sessionId)
            ->setParameter('age', $age)
            ->setMaxResults(75)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getPendingList($id)
    {

        // *** Adultes *** //
        //recuperation des membres adultes dans la file d'attente
        $adultsMembersInPending = $this->findMembers($id, '["ROLE_MEMBER"]', '>', 16);
        
        //recuperation des non membres adultes dans la file d'attente
        $adultsNonMembersInPending = $this->findMembers($id, '["ROLE_NON_MEMBER"]', '>', 16);

        // Concaténation des deux tableaux
        $adultsPendingList = array_merge($adultsMembersInPending, $adultsNonMembersInPending);

        //On limite la liste d'attente à 75 personnes
        $adultsPendingList = array_slice($adultsPendingList,0,75);

        // *** Kids *** //
        //recuperation des membres enfants dans la file d'attente
        $kidsMembersInPending = $this->findMembers($id, '["ROLE_MEMBER"]', '<=', 16);
        
        //recuperation des non membres enfants dans la file d'attente
        $kidsNonMembersInPending = $this->findMembers($id, '["ROLE_NON_MEMBER"]', '<=', 16);

        // Concaténation des deux tableaux
        $kidsPendingList = array_merge($kidsMembersInPending, $kidsNonMembersInPending);

        //On limite la liste d'attente à 15 personnes
        $kidsPendingList = array_slice($kidsPendingList,0,15);
        
        //On crée la liste d'attente finale
        $pendingLists = array('adults'=>$adultsPendingList, 'kids'=>$kidsPendingList);

        return $pendingLists;

    }
    

    /*
    public function findOneBySomeField($value): ?PendingList
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
