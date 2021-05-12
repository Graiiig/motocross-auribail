<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionFormType;
use App\Repository\UserRepository;
use App\Repository\SessionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminSessionController extends AbstractController
{
    public function __construct(UserRepository $userRepo, SessionRepository $sessionRepo)
    {
        $this->userRepo = $userRepo;
        $this->sessionRepo = $sessionRepo;
    }

    /**
     * 
     * @Route("/admin/session/new", name="admin_session_new")
     * @Route("/admin/session/{id}/edit", name="admin_session_edit")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Session $session = null, Request $request, ManagerRegistry $managerRegistry)
    {
        // Build a new Session object if unknown
        if (!$session) {
            $session = new Session();
        }

        // Create the SessionType form
        $form = $this->createForm(SessionFormType::class, $session);
        // Process the form data
        $form->handleRequest($request);
        // If the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Process the form data
            $em = $managerRegistry->getManager();
            // Tell the manager to persist
            $em->persist($session);
            // Send the session in the database
            $em->flush();
            // Redirect to the admin panel
            return $this->redirectToRoute('admin');
        }
        // Render the forma
        return $this->render('admin/__new.html.twig', [
            "sessionForm" => $form->createView()
        ]);
    }
}
