<?php
namespace App\Libraries;
use setasign\Fpdi\Fpdi;
 
class PDF extends FPDI
{
 
    function Footer()
    {
        //numero de página

        $this->SetY(-15);
        $this->SetFont('Arial','',8);
        $this->Cell(0,10,$this->PageNo().'/{nb}',0,0,'R');        
        
        //if ($this->PageFirma == $this->PageNo())
        //{
            $this->SetY(-20);
            $this->SetFont('Arial','',8);
            $this->Cell(0,0,$this->footer_txt,0,0,'C');
            $this->Image($this->imagen_qr,10,8,22);
            $this->SetY(-15);

            //$ancho = $this->ancho;

            $this->SetFont('Arial','',8);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(80,0,$this->footer_id_txt,0,0,'R');

            $this->SetFont('Arial','U',8);
            $this->SetTextColor(0, 0, 255);
            $this->Cell(80,0,$this->footer_link,0,0,'L');
        //}

    }
}