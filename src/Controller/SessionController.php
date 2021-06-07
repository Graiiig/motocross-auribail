<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\User;
use App\Repository\PendingListRepository;
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
     * @Route("/toutes-les-sessions", name="session_all")
     */
    public function allSessions(SessionRepository $sessionRepository, SessionService $sessionService, PendingListRepository $pendingListRepository){
        $nextSessions = $sessionRepository->findNextAll();
        $nextSessionsInfo = []; 
        foreach ($nextSessions as $key => $nextSession) {
            array_push($nextSessionsInfo,$sessionService->getNextSessionInfo($nextSession, $this->getUser(),$sessionRepository, $pendingListRepository));
        }
        return $this->render('session\next-sessions.html.twig', compact('nextSessionsInfo'));
    }
    
}
