<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginAuthenticator $authenticator, MailerInterface $mailer): Response
    {
        // On instancie un utilisateur
        $user = new User();
        // On crée le formulaire d'inscription
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        // Si le formulaire est validé et envoyé
        if ($form->isSubmitted() && $form->isValid()) {
            // On encode le mot de passe
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // On ajoute un rôle à l'utilisateur
            $user->setRoles(['ROLE_NON_MEMBER']);
            // On instancie le manager
            $entityManager = $this->getDoctrine()->getManager();
            // On ajoute un utilisateur
            $entityManager->persist($user);
            // On envoie tout en base de donnée
            $entityManager->flush();
            // On ajoute un email de confirmation
            $email = (new Email())
                ->from('mc.auribail@gmail.com')
                ->to($user->getEmail())
                ->subject('Confirmation inscription')
                ->text('Vous êtes correment inscrit au Auribail MX PARK')
                ->text('Votre identifiant : ' . $user->getEmail());
            // On envoie le email
            $mailer->send($email);

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }
        // On retourne la vue d'inscription
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
