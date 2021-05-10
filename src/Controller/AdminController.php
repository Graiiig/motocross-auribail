<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $sessionRepo = $this->getDoctrine()->getRepository(Session::class);

        $users = $userRepo->findBy([], null, 5);

        $sessions = $sessionRepo->findBy([], null, 5);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users,
            'sessions' =>$sessions
        ]);
    }

    /**
     * @Route("/admin/{id}", name="admin_session")
     */
    public function session($id): Response
    {

        return $this->render('admin/session.html.twig', [
            'id' => $id
        ]);
    }
}
