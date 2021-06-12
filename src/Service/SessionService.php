<?php
// src/Service/SessionService.php
namespace App\Service;

use App\Repository\PendingListRepository;
use App\Repository\SessionRepository;

class SessionService
{
    public function getNextSessionInfo($session, $user, SessionRepository $sessionRepository, PendingListRepository $pendingListRepository): array
    {
        if ($session == null) {
            // *** fonction crée dans le repo *** //
            // Si la session est nulle en argument, on récupère la prochaine
            $session = $sessionRepository->findNext();
        }
                
        //On récupère la liste d'attente de la session
        $users = $pendingListRepository->getPendingList($session);
        
        $usersAdultsAndKids = array_merge($users['adults'], $users['kids']);

        // Pour chaque user dans la PL entière
        for ($i = 0; $i < count($usersAdultsAndKids); $i++) {
            // Si l'utilisateur de la PL = celui qui est connecté, alors, 
            // On lui attribue une position
            if ($usersAdultsAndKids[$i]->getUser() == $user) {
                $position = $i + 1;
                break;
            } else {
                $position = 0;
            }
        }
        
        // On vérifie sur l'utilisateur est dans la session
        $user = $pendingListRepository->findBy(['session' => $session, 'user' => $user]);

        // Vérifie si un user est inscrit
        if ($user) {
            $statusUserThisSession = "signed";
        } else {
            $statusUserThisSession = "notsigned";
        }
        //Calcul des places restantes
        $adultsNb = 75 - count($users['adults']);
        $kidsNb = 15 - count($users['kids']);
        // On retourne un array
        return array(
            "session" => $session,
            "adults" => $adultsNb,
            "children" => $kidsNb,
            "statusUserThisSession" => $statusUserThisSession,
            "position" => $position
        );
    }
}
