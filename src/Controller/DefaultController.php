<?php

namespace App\Controller;

use App\Repository\PendingListRepository;
use App\Repository\SessionRepository;
use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SessionRepository $sessionRepository, SessionService $sessionService, PendingListRepository $pendingListRepository): Response
    {
        // Récupère le User
        $user = $this->getUser();
        // Récupère la prochaine Session
        $nextSession = $sessionService->getNextSessionInfo(null, $user,$sessionRepository, $pendingListRepository);
        $nextSessionTitle = "Prochain entraînement le ". $nextSession['session']->getDate()->format('d/m/Y');

        dump($nextSessionTitle);
        
        // Retourne la page principale
        return $this->render('default/index.html.twig', compact('nextSession', 'nextSessionTitle'));
    }
}
