<?php

namespace App\Controller;

use App\Repository\SessionRepository;
use App\Service\SessionService;
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
}
