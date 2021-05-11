<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/mon-compte", name="user_account")
     */
    public function index(): Response
    {
        if ($this->getUser()){

            $user = $this->getUser();
            $sessions = $user->getSessions();
            
            return $this->render('user/index.html.twig', compact('sessions'));
        }
        else {
            return $this->redirectToRoute('app_login');
        }
    }
}
