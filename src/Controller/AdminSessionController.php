<?php

namespace App\Controller;

use App\Entity\PendingList;
use App\Entity\Session;
use App\Form\SessionFormType;
use App\Repository\PendingListRepository;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminSessionController extends AbstractController
{
    protected $entityManager;

    public function __construct(SessionRepository $sessionRepo, PendingListRepository $pendingListRepo,  UserRepository $userRepo, EntityManagerInterface $entityManager)
    {
        $this->sessionRepo = $sessionRepo;
        $this->pendingListRepo = $pendingListRepo;
        $this->userRepo = $userRepo;
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
        $pendingLists = $this->pendingListRepo->getPendingList($id);
        // On crée un tableau pour récupérer les noms dans la vue twig après
        $names = array(0 => 'adultes', 1 => 'enfants');
        // Va chercher la session dans la base de donnée avec son id
        $session = $this->sessionRepo->findOneBy(['id' => $id]);

        return $this->render('admin/session.html.twig', [
            'session' => $session,
            'pendingLists' => $pendingLists,
            'names' => $names
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
            // Si la session existe déjà, envoie un message flash
            if($session->getId() !== null){
                $this->addFlash('notice', 'Session modifiée avec succès !');
            // Si la session n'existe pas, envoie un message flash
            }else{
                $this->addFlash('success', 'Session ajoutée avec succès !');
            }
            // Dis au manager de persister
            $this->entityManager->persist($session);
            // Envoie les données dans la base
            $this->entityManager->flush();
            // Redirige au panneau d'administration
            return $this->redirectToRoute('admin');
        }
        // Affichage du formulaire dans la vue et redirection
        return $this->render('admin/__new.html.twig', [
            "sessionForm" => $form->createView(),
            "editMode" => $session->getId() !== null
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
        // Selectionne l'entité à supprimer
        $this->entityManager->remove($session);
        // Supprime la session dans la base de donnée
        $this->entityManager->flush();
        // Envoie un message flash
        $this->addFlash('danger','Session supprimée avec succès !');
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

        // Selectionne l'entité à supprimer
        $this->entityManager->remove($pendinglist);
        // Supprimer dans la base de donnée
        $this->entityManager->flush();
        // Envoie un message flash
        $this->addFlash('danger','Utilisateur supprimée de la session avec succès !');
        // Redirige sur la session avec son id
        return $this->redirectToRoute('admin_session', [
            'id' => $sessionId
        ]);
    }

    /**
     * Permets à l'admin d'envoyer un email
     *
     * @Route("/admin/session/{id}/email", name="admin_session_email")
     * @IsGranted("ROLE_ADMIN")
     */
    public function sendMail(Request $request, $id, MailerInterface $mailer)
    {
        // On récupère l'input du titre de l'email
        $title = $request->query->get('emailTitle', []);
        // On récupère l'input du body de l'email
        $body = $request->query->get('emailBody', []);
        // On récupère la pending list avec le session id renseigné
        $userInSession = $this->pendingListRepo->getPendingList($id);
        // On récupère les enfants et les adultes
        $userInSession = array_merge($userInSession['kids'], $userInSession['adults']);
        // On crée un nouvel array pour recevoir les emails
        $userEmails = [];
        // Pour chaque utilisateur
        foreach ($userInSession as $user) {
            // On récupère son email
            $email = $user->getUser()->getEmail();
            // On ajoute l'email dans l'array
            array_push($userEmails, $email);
        }
        // Si les champs de l'email sont remplis
        if ($title != null && $body != null) {
            // On crée un nouvel email
            $email = (new Email())
                // L'expéditeur
                ->from('mc.auribail@gmail.com')
                // Copie caché
                ->bcc(...$userEmails)
                // On envoie le sujet du mail
                ->subject($title)
                // On envoie le contenu du mail
                ->text($body);
            // Envoie du mail avec mailer
            $mailer->send($email);
            // Affiche un message associé
            $this->addFlash('success', "L'email est envoyé !");
        }
        // Si le titre est vide
        else if ($title == null) {
            // Affiche un message d'erreur associé
            $this->addFlash('error', "Merci de renseigner le titre de l'email");
        }
        // Si le contenu est vide
        else {
            // Affiche un message d'erreur associé
            $this->addFlash('error', "Merci de renseigner le contenu de l'email");
        }
        // Renvoie à la session
        return $this->redirectToRoute('admin_session', [
            'id' => $id
        ]);
    }
}
