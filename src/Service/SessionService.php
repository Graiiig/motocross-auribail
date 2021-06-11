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
        // Récupère une pending list par session et user
        $users = $pendingListRepository->findBy(['session' => $session, 'user' => $user]);
        // Récupère les pendinglist pour la session
        $currentSession = $pendingListRepository->findBy(['session' => $session]);
        // Pour chaque pending list
        for ($i = 0; $i < count($currentSession); $i++) {
            // Affecte une position à chaque utilisateur
            if ($currentSession[$i]->getUser() == $user) {
                $position = $i;
                break;
            } else {
                $position = 0;
            }
        }
        // Vérifie si un user est inscrit
        if ($users) {
            $statusUserThisSession = "signed";
        } else {
            $statusUserThisSession = "notsigned";
        }
        //On récupère la liste d'attente de la session
        $pendingLists = $pendingListRepository->getPendingList($session);
        //Calcul des places restantes
        $adultsNb = 75 - count($pendingLists['adults']);
        $kidsNb = 15 - count($pendingLists['kids']);
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
