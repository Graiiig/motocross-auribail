<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\PendingListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;

class GeneratePdfController extends AbstractController
{
    /**
     * @Route("admin/liste-pdf/{currentSession}/{members}", name="generate_pdf")
     */
    public function pdfAction(Pdf $knpSnappyPdf, PendingListRepository $pendingListRepository, Session $currentSession, $members) 
    {

        //On récupère la liste d'attente en fonction du numéro de session indiqué dans l'URL et de son statut
        if ($currentSession->getStatus() == 1) {
            $pendingList = $pendingListRepository->getPendingList($currentSession);
        }
        else {
            $pendingList = $pendingListRepository->getPendingListOfLicensed($currentSession);
        }

        //Si il n'y ni adultes, ni enfants, c-a-d, une PL vide on redirige vers la page home 
        if($pendingList["adults"] == null && $pendingList["children"] == null){
            return $this->redirectToRoute('home');
        }

        //transformation de variable en fonction de ce qui a été choisit dans l'url
        if($members == "enfants"){
            $type = "kids";
        }
        else {
            $type = "adults";
        }

        // On crée un tableau vide qui viendra accueillir la liste par order alphabétique       
        $pendingListAtoZ = [];

        //On parcourt la PL récupérée et on remplit le tableau précedemment crée
        foreach ($pendingList[$type] as $key => $item) {
            $pendingListAtoZ = array_merge($pendingListAtoZ, [$item->getUser()->getLastName()=>["position"=>$key,"lastName"=>$item->getUser()->getLastName(), "firstName"=>$item->getUser()->getFirstName(), "license"=>$item->getUser()->getLicense(), "role"=>$item->getUser()->getRoles()]]);
        }

        // On tri le tableau dans l'ordre ascendant en fonction de l'index (ici le nom du membre)
        ksort($pendingListAtoZ);
        
        // On crée la vue twig avec les données de la PL classée par ordre alphabétique
        $html = $this->renderView('generate_pdf/index.html.twig', array(
            'pendingList' => $pendingListAtoZ,
            'currentSession' => $currentSession,
            'members' => $members
        ));

        //On retourne le PDF
        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'Emargement-'.$members.'-session-numero-'.$currentSession->getId().'.pdf'
        );
    }

}
