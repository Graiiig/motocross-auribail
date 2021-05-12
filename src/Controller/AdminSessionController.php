<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionFormType;
use App\Repository\SessionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminSessionController extends AbstractController
{
    public function __construct(SessionRepository $sessionRepo)
    {
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

    /**
     * @Route("/admin/session/{id}/delete", name="admin_session_delete", methods="DELETE")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete($id, ManagerRegistry $managerRegistry)
    {
        // Remove the session
        $session = $this->sessionRepo->findOneBy(['id' => $id]);
        $em = $managerRegistry->getManager();
        $em->remove($session);
        $em->flush();
        // Redirect to admin panel
        return $this->redirectToRoute('admin');
    }
}
