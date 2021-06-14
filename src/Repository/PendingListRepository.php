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
        $this->roleMember = '["ROLE_MEMBER"]';
        $this->roleNonMember = '["ROLE_NON_MEMBER"]';
        $this->roleAdmin = '["ROLE_ADMIN"]';
    }

    //fonction pour retrouver les membres en fonction de leur age, roles et session
    public function findMembers($sessionId, $role, $operator, $age)
    {
        // On crée une requête dql
        $qb = $this->getEntityManager()->createQueryBuilder();

        return
            $qb->select('pl')
            ->from('App\Entity\PendingList', 'pl')
            ->innerJoin('App\Entity\User', 'u', 'WITH', 'u.id = pl.user')
            ->andWhere('u.roles LIKE :role')
            ->andWhere('pl.session = :sessionId')
            ->andWhere('TIMESTAMPDIFF(YEAR,u.birthdate,CURRENT_DATE()) ' . $operator . ' :age')
            ->setParameter('role', $role)
            ->setParameter('sessionId', $sessionId)
            ->setParameter('age', $age)
            ->orderBy('pl.id')
            ->getQuery()
            ->getResult();
    }
    //fonction pour retrouver les membres en fonction de leur age, roles et session
    public function findMembersWithLicense($sessionId, $role, $operator, $age)
    {
        // On crée une requête dql
        $qb = $this->getEntityManager()->createQueryBuilder();

        return
            $qb->select('pl')
            ->from('App\Entity\PendingList', 'pl')
            ->innerJoin('App\Entity\User', 'u', 'WITH', 'u.id = pl.user')
            ->andWhere('u.roles LIKE :role')
            ->andWhere('pl.session = :sessionId')
            ->andWhere('TIMESTAMPDIFF(YEAR,u.birthdate,CURRENT_DATE()) ' . $operator . ' :age')
            ->andWhere('u.license IS NOT NULL')
            ->andWhere('u.license != :license')
            ->setParameter('role', $role)
            ->setParameter('sessionId', $sessionId)
            ->setParameter('age', $age)
            ->setParameter('license', "")
            ->orderBy('pl.id')
            ->getQuery()
            ->getResult();
    }

    

    // Fonction qui récupère une PL pour une session donnée
    public function getPendingList($sessionId)
    {

        // *** Adultes *** //
        //recuperation des membres admins dans la file d'attente
        $adminsMembersInPending = $this->findMembers($sessionId, $this->roleAdmin, '>', 0);

        //recuperation des membres adultes dans la file d'attente
        $adultsMembersInPending = $this->findMembers($sessionId, $this->roleMember, '>', 16);

        //recuperation des non membres adultes dans la file d'attente
        $adultsNonMembersInPending = $this->findMembers($sessionId, $this->roleNonMember, '>', 16);

        // Concaténation des trois tableaux
        $adultsPendingList = array_merge($adminsMembersInPending, $adultsMembersInPending, $adultsNonMembersInPending);

        //On limite la liste d'attente à 75 personnes
        // $adultsPendingList = array_slice($adultsPendingList, 0, 75);

        // *** Kids *** //
        //recuperation des membres enfants dans la file d'attente
        $kidsMembersInPending = $this->findMembers($sessionId, $this->roleMember, '<=', 16);

        //recuperation des non membres enfants dans la file d'attente
        $kidsNonMembersInPending = $this->findMembers($sessionId, $this->roleNonMember, '<=', 16);

        // Concaténation des deux tableaux
        $kidsPendingList = array_merge($kidsMembersInPending, $kidsNonMembersInPending);

        //On limite la liste d'attente à 15 personnes
        // $kidsPendingList = array_slice($kidsPendingList, 0, 15);

        //On crée la liste d'attente finale
        return array('adults' => $adultsPendingList, 'kids' => $kidsPendingList);
    }


    // Fonction qui récupère une PL pour une session donnée
    public function getPendingListOfLicensed($sessionId)
    {
        // *** Adultes *** //
        //recuperation des membres admins dans la file d'attente
        $adminsMembersInPending = $this->findMembersWithLicense($sessionId, $this->roleAdmin, '>', 0);

        //recuperation des membres adultes dans la file d'attente
        $adultsMembersInPending = $this->findMembersWithLicense($sessionId, $this->roleMember, '>', 16);

        //recuperation des non membres adultes dans la file d'attente
        $adultsNonMembersInPending = $this->findMembersWithLicense($sessionId, $this->roleNonMember, '>', 16);

        // Concaténation des trois tableaux
        $adultsPendingList = array_merge($adminsMembersInPending, $adultsMembersInPending, $adultsNonMembersInPending);

        //On limite la liste d'attente à 75 personnes
        $adultsPendingList = array_slice($adultsPendingList, 0, 75);

        // *** Kids *** //
        //recuperation des membres enfants dans la file d'attente
        $kidsMembersInPending = $this->findMembersWithLicense($sessionId, $this->roleMember, '<=', 16);

        //recuperation des non membres enfants dans la file d'attente
        $kidsNonMembersInPending = $this->findMembersWithLicense($sessionId, $this->roleNonMember, '<=', 16);

        // Concaténation des deux tableaux
        $kidsPendingList = array_merge($kidsMembersInPending, $kidsNonMembersInPending);

        //On limite la liste d'attente à 15 personnes
        $kidsPendingList = array_slice($kidsPendingList, 0, 15);

        //On crée la liste d'attente finale
        return array('adults' => $adultsPendingList, 'kids' => $kidsPendingList);
    }
}
