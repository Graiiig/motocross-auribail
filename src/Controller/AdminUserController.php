<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{

    /**
     * Edition d'un utilisateur
     * 
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     * @IsGranted("ROLE_ADMIN")
     */

    public function edit(Request $request, User $user, EntityManagerInterface $em)
    {
        // On génère le formulaire d'un user
        $form = $this->createForm(AdminFormType::class, $user);
        // Prend en charge la requête
        $form->handleRequest($request);
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Envoie l'utilisateur dans la base de donnée
            $em->persist($user);
            $em->flush();
            // Redirige vers le panneau d'administration
            return $this->redirectToRoute('admin');
        }
        // Génère la vue d'édition d'utilisateur et le formulaire
        return $this->render('admin/edit.html.twig', ['users' => $form->createView()]);
    }
    /**
     * Suppression d'un utilisateur
     * 
     * @Route("/admin/user/{id}/delete", name="admin_user_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete($id, ManagerRegistry $managerRegistry, UserRepository $repo)
    {
        // Trouve l'utilisateur avec son id
        $user = $repo->findOneBy(['id' => $id]);
        // Prend en charge la requête et supprime l'utilisateur dans la bdd
        $em = $managerRegistry->getManager();
        $em->remove($user);
        $em->flush();
        // Redirige vers le panneau d'administration
        return $this->redirectToRoute('admin');
    }
}
