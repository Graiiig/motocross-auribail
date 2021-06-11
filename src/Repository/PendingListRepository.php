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
            ->setMaxResults(75)
            ->getQuery()
            ->getResult();
    }

    // Fonction qui récupère une PL pour une session donnée
    public function getPendingList($sessionId)
    {
        // *** Adultes *** //
        //recuperation des membres admins dans la file d'attente
        $adminsMembersInPending = $this->findMembers($sessionId, '["ROLE_ADMIN"]', '>', 0);

        //recuperation des membres adultes dans la file d'attente
        $adultsMembersInPending = $this->findMembers($sessionId, '["ROLE_MEMBER"]', '>', 16);

        //recuperation des non membres adultes dans la file d'attente
        $adultsNonMembersInPending = $this->findMembers($sessionId, '["ROLE_NON_MEMBER"]', '>', 16);

        // Concaténation des trois tableaux
        $adultsPendingList = array_merge($adminsMembersInPending, $adultsMembersInPending, $adultsNonMembersInPending);

        //On limite la liste d'attente à 75 personnes
        $adultsPendingList = array_slice($adultsPendingList, 0, 75);

        // *** Kids *** //
        //recuperation des membres enfants dans la file d'attente
        $kidsMembersInPending = $this->findMembers($sessionId, '["ROLE_MEMBER"]', '<=', 16);

        //recuperation des non membres enfants dans la file d'attente
        $kidsNonMembersInPending = $this->findMembers($sessionId, '["ROLE_NON_MEMBER"]', '<=', 16);

        // Concaténation des deux tableaux
        $kidsPendingList = array_merge($kidsMembersInPending, $kidsNonMembersInPending);

        //On limite la liste d'attente à 15 personnes
        $kidsPendingList = array_slice($kidsPendingList, 0, 15);

        //On crée la liste d'attente finale
        return array('adults' => $adultsPendingList, 'kids' => $kidsPendingList);
    }
}
