<?php

namespace App\Controller;

use App\Entity\Session;
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
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $user = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // return $this->redirectToRoute('task_success');
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
