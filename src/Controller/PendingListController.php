<?php

namespace App\Controller;

use App\Entity\PendingList;
use App\Entity\Session;
use App\Repository\PendingListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PendingListController extends AbstractController
{
    /**
     * @Route("/session/{session}", name="session")
     */
    public function sessionRegistration(EntityManagerInterface $entityManager, Session $session, PendingListRepository $pendingListRepo, MailerInterface $mailer): Response
    {
        $users = $pendingListRepo->findBy(['session' => $session, 'user' => $this->getUser()]);
        // Si l'utilisateur est déjà inscrit OU la session est fermée
        if ($users || $session->getStatus() == false) {
            // On ajoute un message d'erreur
            $this->addFlash('error', 'Cette session n\'est pas disponible');
            // On redirige vers la page principal
            return $this->redirectToRoute('home');
        }
        // Si l'utilisateur est connecté 
        if ($this->getUser()) {
            // On récupère ses informations
            $user = $this->getUser();
        }
        //On créé une nouvelle entrée dans pending list
        $pendingList = new PendingList();
        //On set les infos nécessaires
        $pendingList->setUser($user)
            ->setSession($session)
            ->setDatetime(new \DateTime());
        // On envoie en base de donnée
        $entityManager->persist($pendingList);
        $entityManager->flush();

        // Ajout d'un email de confirmation
        $email = (new Email())
            ->from('mc.auribail@gmail.com')
            ->to($user->getEmail())
            ->subject('Inscription à l\'entrainement du ' . $session->getDate()->format('d-m-Y') . '')
            ->text('Vous êtes correctement inscrit à ' . $session->getTitle());
        // Envoie du mail
        $mailer->send($email);
        // Ajoute un message de confirmation
        $this->addFlash('success', 'Vous êtes bien inscrit à l\'entrainement');
        // Redirige vers la page d'accueil
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/session/{session}/unsubscription", name="session_unsubscribe")
     */
    public function sessionUnsubscription(Session $session, PendingListRepository $pendingListRepo, ManagerRegistry $managerRegistry)
    {
        // Récupère l'entrainement à supprimer
        $pendingList = $pendingListRepo->findBy(['session' => $session, 'user' => $this->getUser()]);
        // Prend en charge la requête
        $em = $managerRegistry->getManager();
        // Supprime l'entrainement (pending list) associé
        $em->remove($pendingList[0]);
        // Envoie en base de donnée
        $em->flush();
        // Affiche un message de confirmation
        $this->addFlash('danger', 'Vous êtes désinscrit de l\'entrainement');
        // Redirige vers la page d'accueil
        return $this->redirectToRoute('home');
    }
}
