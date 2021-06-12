<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Repository\PendingListRepository;
use App\Repository\SessionRepository;
use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/mon-compte", name="user_account")
     */
    public function index(SessionService $sessionService, SessionRepository $sessionRepository, Request $request, PendingListRepository $pendingListRepository): Response
    {
        // On vérifie que l'utilisateur est connecté
        if ($this->getUser()){
            // On récupère les informations de l'utilisateur
            $user = $this->getUser();
            $sessions = $user->getPendingLists();
            // On récupère la prochaine session en date
            $nextSession = $sessionService->getNextSessionInfo(null, $user,$sessionRepository, $pendingListRepository);
            // On crée le formulaire d'édition
            $form = $this->createForm(UserFormType::class, $user);
            // On prend en charge la requête
            $form->handleRequest($request);
            // Si le formulaire est envoyé et valide
            if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les informations
            $user = $form->getData();
            // On envoie les nouvelles informations en base de donnée
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // On ajoute un message d'information
            $this->addFlash('success','Vos informations ont été mises à jour !'); 
        }
            // On génère la vue
            return $this->render('user/index.html.twig', 
            [   'sessions'=>$sessions,
                'nextSession' =>$nextSession,
                'form' => $form->createView(),
            ]);
        }
        else {
            return $this->redirectToRoute('app_login');
        }
    }
}
