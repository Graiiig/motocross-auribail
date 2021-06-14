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
            // Si la session est nulle en argument, on récupère la prochaine
            $session = $sessionRepository->findNext();
        }

        //On récupère la liste d'attente de la session
        $users = $pendingListRepository->getPendingList($session);

        $currentYear = new \DateTime();

        if ($user && ($currentYear->format('Y') - $user->getBirthdate()->format('Y') >= 16)) {
            $usersInPendingList = $users['adults'];
        } else {
            // dd($users['kids']);
            $usersInPendingList = $users['kids'];
        }


        // On vérifie sur l'utilisateur est dans la session
        $userSigned = $pendingListRepository->findBy(['session' => $session, 'user' => $user]);

        // Si l'utilisateur est connecté
        if ($userSigned) {
            // On vérifie si une des PL (adultes ou enfants) existe
            if ($users['adults'] || $users['kids']) {

                //Si oui on parcourt la PL
                for ($i = 0; $i < count($usersInPendingList); $i++) {
                    
                    // Si l'utilisateur de la PL = celui qui est connecté, alors, 
                    // On lui attribue une position et un statut
                    if ($usersInPendingList[$i]->getUser() == $user) {
                        $statusUserThisSession = "signed";
                        $position = $i + 1;
                        break;
                    } else {
                        $statusUserThisSession = "notsigned";
                        $position = '';
                    }
                }
            }
        } else {
            $statusUserThisSession = "notsigned";
            $position = '';
        }

        //Calcul des places restantes
        $adultsNb = $this->usersPendingListCounter(75, count($users['adults']));
        $kidsNb = $this->usersPendingListCounter(15, count($users['kids']));

        // dump(count($users['adults']));
        // dump(count($users['kids']));
        // On retourne un array
        return array(
            "session" => $session,
            "adults" => $adultsNb,
            "children" => $kidsNb,
            "statusUserThisSession" => $statusUserThisSession,
            "position" => $position,
            "totalAdults" => count($users['adults']),
            "totalKids" => count($users['kids']),
            
        );
    }

    // Fonction pour compter le nombre de personnes dans la PL
    function usersPendingListCounter($maxUsers, $usersInPl)
    {
        if (($maxUsers - $usersInPl) >= 0) {
            return $maxUsers - $usersInPl;
        } else {
            return 0;
        }
    }
}
