<?php
namespace App\Libraries;
use setasign\Fpdi\Fpdi;
 
class PDF extends FPDI
{
 
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','U',8);
        // Page number
        //$this->MultiCell();
        //$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');

        if ($this->PageFirma == $this->PageNo())
            $this->Write(5, $this->footer_txt, $this->footer_link, false, 'C', true);
    }
}