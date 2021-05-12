<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(UserRepository $userRepo, SessionRepository $sessionRepo)
    {
        $this->userRepo = $userRepo;
        $this->sessionRepo = $sessionRepo;
    }

    /**
     * Affichage du panneau d'administration
     * 
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        // On prend tout les utilisateurs de la base de donnée
        $users = $this->userRepo->findBy([], null, 5);
        // On prend toutes les sessions de la base de donnée
        $sessions = $this->sessionRepo->findBy([], null, 5);
        // On génère la vue avec les variables
        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'sessions' => $sessions
        ]);
    }
}
