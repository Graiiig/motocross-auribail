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
        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
