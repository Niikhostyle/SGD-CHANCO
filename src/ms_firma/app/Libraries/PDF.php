<?php
namespace App\Libraries;
use setasign\Fpdi\Fpdi;
 
class PDF extends FPDI
{
 
    function Footer()
    {
        //if ($this->PageFirma == $this->PageNo())
        //{
            // Position at 1.5 cm from bottom
            
            // Arial italic 8
            
            // Page number
            //$this->MultiCell();
            //$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
           
            if ($this->PageFirma == $this->PageNo())
            {
                $this->SetY(-20);
                $this->SetFont('Arial','',8);
                $this->Cell(0,0,$this->footer_txt,0,0,'C');
                $this->SetY(-15);
                $this->SetFont('Arial','U',8);
                $this->SetTextColor(0, 0, 255);
                $this->Cell(0,0,$this->footer_link,0,0,'C');
            }
                

                //$this->Text(0,10,$text,0,false,true,0,'C',false);    
            //$this->Cell(0,10,$this->footer_txt,0,0,'C');

        
            //$this->Write(0, $this->footer_txt, $this->footer_link);
        //}
    }
}