<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\User;
use App\Repository\SessionRepository;
use App\Service\SessionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SessionController extends AbstractController
{
    /**
     * @Route("/session/{id}", name="session")
     */
   

    public function sessionRegistration(EntityManagerInterface $entityManager, Session $session): Response
    {   
        if ($this->getUser())
        {

            $user = $this->getUser(); 
        }
        $session->addUser($user); 
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager-> persist($session);
        $entityManager->flush();  

        return $this->render('session\session.html.twig', compact('session'));
        
    }

    /**
     * @Route("/toutes-les-sessions", name="session_all")
     */
    public function allSessions(SessionRepository $sessionRepository, SessionService $sessionService){
        $nextSessions = $sessionRepository->findNextAll();
        $nextSessionsInfo = []; 
        foreach ($nextSessions as $key => $nextSession) {
            array_push($nextSessionsInfo,$sessionService->getNextSessionInfo($nextSession,$sessionRepository));
        }
        return $this->render('session\next-sessions.html.twig', compact('nextSessionsInfo'));
    }
    
}
