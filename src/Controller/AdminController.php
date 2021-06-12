<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    public function __construct(UserRepository $userRepo, SessionRepository $sessionRepo)
    {
        $this->userRepo = $userRepo;
        $this->sessionRepo = $sessionRepo;
    }

    /**
     * Affichage du panneau d'administration
     * 
     * @Route("/admin", name="admin")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        // recuperer l'input de recherche
        $input = $request->query->get('search', []);

        // Test si champ vide = tout afficher Sinon test du firstName -> lastName -> License
        if ($input != null) {
            if($this->userRepo->findByLastName($input) == null){
                if ($this->userRepo->findByfirstName($input) == null) {
                    $users = $this->userRepo->findByLicense($input);
                } else {
                    $users = $this->userRepo->findByfirstName($input);
                }
            }
            else {
                $users = $this->userRepo->findByLastName($input);
            }
        } else {
            $users = $this->userRepo->findBy([], null);
        }
        // Pagination des résultats du test (tri de users)
        $userPagination = $paginator->paginate($users, $request->query->getInt('page1', 1), 10, ['pageParameterName' => 'page1']);
        // On prend toutes les sessions de la base de données
        $sessions = $this->sessionRepo->findAll();
        // On paginate les session
        $sessionPagination = $paginator->paginate($sessions, $request->query->getInt('page2', 1), 5, ['pageParameterName' => 'page2']);
        // On génère la vue avec les variables
        return $this->render('admin/index.html.twig', [
            'users' => $userPagination,
            'sessions' => $sessionPagination
        ]);
    }
}
