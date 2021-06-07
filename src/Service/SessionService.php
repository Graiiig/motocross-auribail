<?php 
// src/Service/SessionService.php
namespace App\Service;

use App\Entity\Session;
use App\Repository\PendingListRepository;
use App\Repository\SessionRepository;

class SessionService
{
    public function getNextSessionInfo($session, $user, SessionRepository $sessionRepository, PendingListRepository $pendingListRepository): array
    {
        if ($session == null){

            $session = $sessionRepository->findNext(); // *** fonction crée dans le repo *** //
        }

        $users = $pendingListRepository->findBy(['session'=>$session, 'user'=>$user]);
        if($users){
            $statusUserThisSession = "signed";
        }
        else {
            $statusUserThisSession = "notsigned";
        }
        
        // *** Le nombre d'utilisateurs dans une session *** //
        $usersInSession = count($session->getUser());

        // *** Calcul des âges pour déterminer les places restantes dans chaque
        // *** catégorie
        $adults = 75;
        $children = 15;
        foreach ($session->getUser() as $key => $user) {
            $today = new \DateTime(); 
            $interval = $today->diff($user->getBirthdate());
            // dump($interval);
            if (intval($interval->format('%Y')) < 16) {
                $children--;
            }
            else {
                $adults--;
            }
        }
        $sessionInfo = array(
            "session"=>$session,
            "adults"=> $adults,
            "children"=>$children,
            "usersInSession" => $usersInSession,
            "statusUserThisSession" => $statusUserThisSession
        );
        return $sessionInfo;
    }
}