<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function edit(Request $request, User $user, EntityManagerInterface $em){

        $form = $this->createForm(AdminFormType::class, $user);

        $form->handleRequest($request); 

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($user);
            $em->flush(); 
            return $this->redirectToRoute('admin_edit');
        }

        return $this->render('admin/index.html.twig', ['users' => $user]);
    }
}
