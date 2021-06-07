<?php

namespace App\Controller;

use App\Entity\Session;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;

class GeneratePdfController extends AbstractController
{
    /**
     * @Route("/liste-pdf/{session}", name="generate_pdf_list")
     */
    public function pdfAction(Pdf $knpSnappyPdf, Session $session) 
    {
        
        
        $html = $this->renderView('generate_pdf/index.html.twig', array(
            'session' => 'session'
        ));

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'liste-pdf-session-numero-'.$session->getId().'.pdf'
        );
    }
}
