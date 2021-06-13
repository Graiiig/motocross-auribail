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
        if ($this->getUser()){
            // On récupère les informations de l'utilisateur
            $user = $this->getUser();
            $sessions = $user->getPendingLists();
            // On récupère la prochaine session en date
            $nextSession = $sessionService->getNextSessionInfo(null, $user,$sessionRepository, $pendingListRepository);
            // On crée le formulaire d'édition
            $form = $this->createForm(UserFormType::class, $user);
            // On prend en charge la requête
            $form->handleRequest($request);
            // Si le formulaire est envoyé et valide
            if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les informations
            $user = $form->getData();

                        /** @var UploadedFile $profilePicture */
                        $profilePictureFile = $form->get('profilePicture')->getData();

                        // this condition is needed because the 'profilePicture' field is not required
                        // so the PDF file must be processed only when a file is uploaded
                        if ($profilePictureFile) {
                            $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                            // this is needed to safely include the file name as part of the URL
                            $safeFilename = $slugger->slug($originalFilename);
                            $newFilename = $safeFilename.'-'.uniqid().'.'.$profilePictureFile->guessExtension();
            
                            // Move the file to the directory where profilePictures are stored
                            try {
                                $profilePictureFile->move(
                                    $this->getParameter('profilePictures_directory'),
                                    $newFilename
                                );
                            } catch (FileException $e) {
                                // ... handle exception if something happens during file upload
                            }
            
                            // updates the 'brochureFilename' property to store the PDF file name
                            // instead of its contents
                            $user->setProfilePicture($newFilename);
                        }

            // On envoie les nouvelles informations en base de donnée
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // On ajoute un message d'information
            $this->addFlash('success','Vos informations ont été mises à jour !'); 
        }
            // On génère la vue
            return $this->render('user/index.html.twig', 
            [   'sessions'=>$sessions,
                'nextSession' =>$nextSession,
                'form' => $form->createView(),
            ]);
        }
        else {
            return $this->redirectToRoute('app_login');
        }
    }
}
