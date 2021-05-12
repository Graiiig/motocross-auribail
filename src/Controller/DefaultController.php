<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\SessionRepository;
use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SessionRepository $sessionRepository, SessionService $sessionService): Response
    {
        $nextSession = $sessionService->getNextSessionInfo($sessionRepository);

        return $this->render('default/index.html.twig', compact('nextSession'));
    }
}
