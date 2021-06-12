<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateurest déjà connecté
        if ($this->getUser()) {
            // Redirige sur l'index du user 
            return $this->redirectToRoute('user_account');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // Si il y a une erreur
        if ($error) {
            // Ajoute un message d'erreur
            $this->addFlash('danger', $error->getMessageKey());
        }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        // Retourne la page du login
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
