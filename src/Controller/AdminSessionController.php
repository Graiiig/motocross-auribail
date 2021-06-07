<?php

namespace App\Controller;

use App\Entity\PendingList;
use App\Entity\Session;
use App\Form\SessionFormType;
use App\Repository\PendingListRepository;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminSessionController extends AbstractController
{
    protected $entityManager;

    public function __construct(SessionRepository $sessionRepo, PendingListRepository $pendingListRepository, EntityManagerInterface $entityManager)
    {
        $this->sessionRepo = $sessionRepo;
        $this->pendingListRepository = $pendingListRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Affichage d'une session
     * 
     * @Route("/admin/session/{id}", name="admin_session")
     * @IsGranted("ROLE_ADMIN")
     */
    public function show($id)
    {
        //Fonction pour récupérer la pending list (voir PendingListRepository)
        $pendingLists = $this->pendingListRepository->getPendingList($id);

        // On crée un tableau pour récupérer les noms dans la vue twig après
        $names = array(0 => 'adultes', 1 => 'enfants');

        // Va chercher la session dans la base de donnée avec son id
        $session = $this->sessionRepo->findOneBy(['id' => $id]);

        return $this->render('admin/session.html.twig', [
            'session' => $session,
            'pendingLists' => $pendingLists,
            'names'=>$names
        ]);
    }

    /**
     * Création et Edition d'une session
     * 
     * @Route("/admin/creation-session", name="admin_session_new")
     * @Route("/admin/session/{id}/edit", name="admin_session_edit")
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

            // Dis au manager de persister
            $this->entityManager->persist($session);
            // Envoie les données dans la base
            $this->entityManager->flush();
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
     * @Route("/admin/session/{id}/delete", name="admin_session_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteSession($id)
    {
        // Récupère la session avec son id
        $session = $this->sessionRepo->findOneBy(['id' => $id]);
        // Prend en charge les données
        ;
        // Supprime la session dans la base de donnée
        $this->entityManager->remove($session);
        $this->entityManager->flush();
        // Redirige vers le panneau d'administration
        return $this->redirectToRoute('admin');
    }

    /**
     * Suppression d'un utilisateur dans une session
     * 
     * @Route("/delete-user/{pendinglist}/{sessionId}", name="admin_session_user_delete", methods="DELETE")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteUser($sessionId, PendingList $pendinglist, ManagerRegistry $managerRegistry)
    {
        
        // Supprime la session dans la base de donnée
        $this->entityManager->remove($pendinglist);
        
        $this->entityManager->flush();

        // Redirige sur la session avec son id
        return $this->redirectToRoute('admin_session', [
            'id' => $sessionId
        ]);
    }
}
