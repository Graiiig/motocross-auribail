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
        $user = $pendingListRepository->findBy(['session' => $session, 'user' => $user]);

        // Vérifie si un user est inscrit
        if ($user) {
            $statusUserThisSession = "signed";
            if ($users['adults'] || $users['kids']) {


                for ($i = 0; $i < count($usersInPendingList); $i++) {
                    
                    // Si l'utilisateur de la PL = celui qui est connecté, alors, 
                    // On lui attribue une position
                    if ($usersInPendingList[$i]->getUser() == $user[0]->getUser()) {
                        
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

        dump($statusUserThisSession);

        //Calcul des places restantes
        $adultsNb = $this->usersPendingListCounter(75, count($users['adults']));
        $kidsNb = $this->usersPendingListCounter(15, count($users['kids']));
        // On retourne un array
        return array(
            "session" => $session,
            "adults" => $adultsNb,
            "children" => $kidsNb,
            "statusUserThisSession" => $statusUserThisSession,
            "position" => $position
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
