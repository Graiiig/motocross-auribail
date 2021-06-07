<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionFormType;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminSessionController extends AbstractController
{
    public function __construct(SessionRepository $sessionRepo)
    {
        $this->sessionRepo = $sessionRepo;
    }

    /**
     * Affichage d'une session
     * 
     * @Route("/admin/session/{id}", name="admin_session")
     * @IsGranted("ROLE_ADMIN")
     */
    public function show($id)
    {
        // Va chercher la session dans la base de donnée avec son id
        $session = $this->sessionRepo->findOneBy(['id' => $id]);
        // Récupère les utilisateurs inscrit en session
        $users = $session->getUser();
        // Affiche la vue de session avec ses variables
        
        return $this->render('admin/session.html.twig', [
            'session' => $session,
            'users' => $users
        ]);
    }

    /**
     * Création et Edition d'une session
     * 
     * @Route("/admin/creation-session", name="admin_session_new")
     * @Route("/admin/session/{id}/edit", name="admin_session_edit", requirements={"id":"\d+"})
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Session $session = null, Request $request, ManagerRegistry $managerRegistry)
    {
        // Création d'un objet session si ce dernier n'existe pas dans le cas d'une modif
        if (!$session) {
            $session = new Session();
        }

        // Création du formulaire
        $form = $this->createForm(SessionFormType::class, $session);
        // Prend en charge les données du formulaire
        $form->handleRequest($request);
        // Si le formulaire est soumis et validé
        if ($form->isSubmitted() && $form->isValid()) {
            // Process the form data
            $em = $managerRegistry->getManager();
            // Dis au manager de persister
            $em->persist($session);
            // Envoie les données dans la base
            $em->flush();
            // Redirige au panneau d'administration
            return $this->redirectToRoute('admin');
        }
        // Affichage du formulaire dans la vue et redirection
        return $this->render('admin/__new.html.twig', [
            "sessionForm" => $form->createView()
        ]);
    }

    /**
     * Suppression d'une Session
     * 
     * @Route("/admin/session/{id}/delete", name="admin_session_delete", methods="DELETE")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteSession($id, ManagerRegistry $managerRegistry)
    {
        // Récupère la session avec son id
        $session = $this->sessionRepo->findOneBy(['id' => $id]);
        // Prend en charge les données
        $em = $managerRegistry->getManager();
        // Supprime la session dans la base de donnée
        $em->remove($session);
        $em->flush();
        // Redirige vers le panneau d'administration
        return $this->redirectToRoute('admin');
    }

    /**
     * Suppression d'un utilisateur dans une session
     * 
     * @Route("/admin/session/{id}/user/{idUser}/delete", name="admin_session_user_delete", methods="DELETE")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteUser($id, $idUser, UserRepository $repo, ManagerRegistry $managerRegistry)
    {
        // Récupère une session avec son id
        $session = $this->sessionRepo->findOneBy(['id' => $id]);
        // Récupère un user avec son id
        $user = $repo->findOneBy(['id' => $idUser]);
        // Requête pour supprimer l'utilisateur dans la session
        $req =  $session->removeUser($user);
        // On instancie le ManagerRegistry
        $em = $managerRegistry->getManager();
        // Persiste la requête et envoie en base de donnée
        $em->persist($req);
        $em->flush();
        // Redirige sur la session avec son id
        return $this->redirectToRoute('admin_session', [
            'id' => $id
        ]);
    }
}
