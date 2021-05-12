<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\User;
use App\Form\AdminFormType;
use App\Repository\UserRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {


        $users = $this->userRepo->findBy([], null, 5);

        $sessions = $this->sessionRepo->findBy([], null, 5);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users,
            'sessions' => $sessions
        ]);
    }

    /**
     * @Route("/admin/{id}", name="admin_session")
     */
    public function session($id): Response
    {

        $session = $this->sessionRepo->findOneBy(['id' => $id]);

        $users = $session->getUser();

        return $this->render('admin/session.html.twig', [
            'session' => $session,
            'users' => $users
        ]);
    }
    /**
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     * @IsGranted("ROLE_ADMIN")
     */

    public function edit(Request $request, User $user, EntityManagerInterface $em)
    {
         
        $form = $this->createForm(AdminFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('admin'); 
            
        }

        return $this->render('admin/edit.html.twig', ['users' => $form->createView()]);
    }
    /**
     * @Route("/admin/user/{id}/delete", name="admin_user_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete($id, ManagerRegistry $managerRegistry)
    {
        // Remove the user 
        $user = $this->userRepo->findOneBy(['id' => $id]);
        $em = $managerRegistry->getManager();
        $em->remove($user);
        $em->flush();
        // Redirect to admin panel
        return $this->redirectToRoute('admin');
    }
}
