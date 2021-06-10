<?php

namespace App\Controller;

use App\Entity\PendingList;
use App\Entity\Session;
use App\Entity\User;
use App\Repository\PendingListRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PendingListController extends AbstractController
{
    /**
     * @Route("/session/{session}", name="session")
     */
    public function sessionRegistration(EntityManagerInterface $entityManager, Session $session, PendingListRepository $pendingListRepository): Response
    {
        $users = $pendingListRepository->findBy(['session' => $session, 'user' => $this->getUser()]);
        // Si l'utilisateur est déjà inscrit OU la session est fermée
        if ($users || $session->getStatus() == false) {
            // TODO Ajouter flash ou noty
            return $this->redirectToRoute('home');
        }

        // Si l'utilisateur est connecté, on récupère ses informations
        if ($this->getUser()) {
            $user = $this->getUser();
        }

        //On créé une nouvelle entrée dans pending list
        $pendingList = new PendingList();

        //On set les infos nécessaires
        $pendingList->setUser($user)
            ->setSession($session)
            ->setDatetime(new \DateTime());

        $entityManager->persist($pendingList);
        $entityManager->flush();

        return $this->render('session\session.html.twig', compact('session'));
    }
}
