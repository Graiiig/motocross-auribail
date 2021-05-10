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
        // $em = $this->getDoctrine()->getManager();
        // $sessionRepo = $em->getRepository(Session::class);
        $session = $this->getDoctrine()
            ->getRepository(Session::class)
            ->findNext();
        dump(count($session->getUser()));

        $adults = 0;
        $children = 0;
        foreach ($session->getUser() as $key => $user) {
            $today = new \DateTime(); 
            $interval = $today->diff($user->getBirthdate());
            // dump (intval($interval->format('%Y')));
            if (intval($interval->format('%Y')) < 16) {
                $children++;
            }
            else {
                $adults++;
            }


            // dump($user);
        }
        return $this->render('default/index.html.twig', [
            "session"=>$session,
            "adults"=> $adults,
            "children"=>$children
        ]);
    }
}
