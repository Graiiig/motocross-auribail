<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Repository\PendingListRepository;
use App\Repository\SessionRepository;
use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/mon-compte", name="user_account")
     */
    public function index(SessionService $sessionService, SessionRepository $sessionRepository, Request $request, PendingListRepository $pendingListRepository, SluggerInterface $slugger): Response
    {
        // On vérifie que l'utilisateur est connecté
        if ($this->getUser()) {
            // On récupère les informations de l'utilisateur
            $user = $this->getUser();
            $sessions = $user->getPendingLists();
            $nextUserSessions = [];
            $previousUserSessions = [];
            $now = new \DateTime();

            //Pour chaque session de l'utilisateur
            foreach ($sessions as $key => $session) {
                // Si l'entrainement est déjà passé
                if ($now->diff($session->getSession()->getDate())->invert === 1) {
                    // On l'ajoute au tableau $previousUserSessions
                    array_push($previousUserSessions, $session);
                } else {
                    // Sinon on l'ajoute au tableau $nextUserSessions
                    array_push($nextUserSessions, $session);
                }
            }
            // On récupère la prochaine session en date
            $nextSession = $sessionService->getNextSessionInfo(null, $user, $sessionRepository, $pendingListRepository);
            // On crée le formulaire d'édition
            $form = $this->createForm(UserFormType::class, $user);
            // On prend en charge la requête
            $form->handleRequest($request);
            // Si le formulaire est envoyé et valide
            if ($form->isSubmitted() && $form->isValid()) {
                // On récupère les informations du formulaire
                $user = $form->getData();

                /** @var UploadedFile $profilePicture */
                $profilePictureFile = $form->get('profilePicture')->getData();

                // this condition is needed because the 'profilePicture' field is not required
                // so the IMG file must be processed only when a file is uploaded
                if ($profilePictureFile) {
                    $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $profilePictureFile->guessExtension();

                    // Move the file to the directory where profilePictures are stored
                    try {
                        $profilePictureFile->move(
                            $this->getParameter('profilePictures_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    // updates the 'ProfilePicture' property to store the IMG file name
                    // instead of its contents
                    $user->setProfilePicture($newFilename);
                }

                // On envoie les nouvelles informations en base de donnée
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                // On ajoute un message d'information
                $this->addFlash('success', 'Vos informations ont été mises à jour !');
            }
            // On génère la vue
            return $this->render(
                'user/index.html.twig',
                [
                    'sessions' => $sessions,
                    'nextSession' => $nextSession,
                    'form' => $form->createView(),
                    'nextUserSessions' => $nextUserSessions,
                    'previousUserSessions' => $previousUserSessions
                ]
            );
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * Permets à l'utilisateur d'envoyer un email à l'admin
     *
     * @Route("/mon-compte/email", name="user_account_email")
     */
    public function sendEmail(Request $request, MailerInterface $mailer)
    {
        // On vérifie qu'un utilisateur est connecté
        if($this->getUser())
        {
            // On récupère l'input du titre de l'email
            $title = $request->query->get('emailTitle', []);
            // On récupère l'input du body de l'email
            $body = $request->query->get('emailBody', []);
            // Si les champs de l'email sont remplis
            if ($title != null && $body != null) {
                // On crée un nouvel email
                $email = (new Email())
                    // L'expéditeur
                    ->from($this->getUser()->getEmail())
                    // Le destinataire
                    ->to('mc.auribail@gmail.com')
                    // On envoie le sujet du mail
                    ->subject($title)
                    // On envoie le contenu du mail
                    ->text($body);
                // Envoie du mail avec mailer
                $mailer->send($email);
                // Affiche un message associé
                $this->addFlash('success', "Le message est envoyé !");
                // On redirige sur l'index user
                return $this->redirectToRoute('user_account');
            }
            // Si le titre est vide
            else if ($title == null) {
                // Affiche un message d'erreur associé
                $this->addFlash('error', "Merci de renseigner un titre");
            }
            // Si le contenu est vide
            else {
                // Affiche un message d'erreur associé
                $this->addFlash('error', "Merci de renseigner un message");
            }
        }
        // Si pas d'utilisateur connecté renvoie versla page de connexion
        else
        {
            return $this->redirectToRoute('app_login'); 
        }
    }
}
