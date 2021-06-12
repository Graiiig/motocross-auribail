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
        if ($this->getUser()){

            $user = $this->getUser();
            $sessions = $user->getPendingLists();

            $nextSession = $sessionService->getNextSessionInfo(null, $user,$sessionRepository, $pendingListRepository);

            $form = $this->createForm(UserFormType::class, $user);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
               'success',
               'Vos informations ont été mises à jour !'
            ); 
        }
            
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
