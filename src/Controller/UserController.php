<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\SessionRepository;
use App\Service\SessionService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/mon-compte", name="user_account")
     */
    public function index(SessionService $sessionService, SessionRepository $sessionRepository): Response
    {
        if ($this->getUser()){

            $user = $this->getUser();
            $sessions = $user->getSessions();

            $nextSession = $sessionService->getNextSessionInfo($sessionRepository);
            
            return $this->render('user/index.html.twig', compact('sessions', 'nextSession'));
        }
        else {
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("quitter-session/{session}", name="user_leave_session")
     */
    public function leaveSession (Session $session)
    {
        // dd($session);
        $currentUser = $this->getUser();
        $currentUser->removeSession($session);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($currentUser);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }
}
