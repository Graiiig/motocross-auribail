<?php

namespace App\Controller;

use App\Entity\PendingList;
use App\Entity\Session;
use App\Repository\PendingListRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;

class GeneratePdfController extends AbstractController
{
    /**
     * @Route("/liste-pdf/{currentSession}", name="generate_pdf_list")
     */
    public function pdfAction(Pdf $knpSnappyPdf, PendingListRepository $pendingListRepository, Session $currentSession) 
    {
        $pendingList = $pendingListRepository->getPendingList($currentSession);
        if($pendingList["adults"] == null && $pendingList["children"] == null){
            return $this->redirectToRoute('home');
        }
        dump($pendingList);
        // TODO affichage de la pl dans le dpf
        $html = $this->renderView('generate_pdf/index.html.twig', array(
            'pendingList' => $pendingList,
            'currentSession' => $currentSession
        ));

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'liste-pdf-session-numero-'.$currentSession->getId().'.pdf'
        );
    }
}
