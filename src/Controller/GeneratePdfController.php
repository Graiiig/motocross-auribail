<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;

class GeneratePdfController extends AbstractController
{
    /**
     * @Route("/generate/pdf", name="generate_pdf")
     */
    public function pdfAction(Pdf $knpSnappyPdf) 
    {
        $html = $this->renderView('generate_pdf/index.html.twig', array(
            'controller_name'  => 'Hello'
        ));

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'file.pdf'
        );
    }
}
