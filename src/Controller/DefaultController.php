<?php

namespace App\Controller;

use App\Entity\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $session = $this->getDoctrine()
            ->getRepository(Session::class)
            ->findNext();
        
        // *** Le nombre d'utilisateurs dans une session *** //
        $usersInSession = count($session->getUser());

        // *** Calcul des âges pour déterminer les places restantes dans chaque
        // *** catégorie
        $adults = 0;
        $children = 0;
        foreach ($session->getUser() as $key => $user) {
            $today = new \DateTime(); 
            $interval = $today->diff($user->getBirthdate());
            if (intval($interval->format('%Y')) < 16) {
                $children++;
            }
            else {
                $adults++;
            }
        }
        $children = 15 - $children;
        $adults = 75 - $adults;
        return $this->render('default/index.html.twig', [
            "session"=>$session,
            "adults"=> $adults,
            "children"=>$children,
            "usersInSession" => $usersInSession
        ]);
    }
}
