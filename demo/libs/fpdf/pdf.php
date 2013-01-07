<?php
require('fpdf.php');

class PDF extends FPDF
{
        public $title;
        function title($title)
        {
                $this->title = $title; 
        }
        //Page header
        function Header()
        {
            //Logo
            //$this->Image('logo_pb.png',10,8,33);
            //Arial bold 15
            $this->SetFont('Arial','B',25);
            //Move to the right
            $this->Cell(80);
            //Title
            $this->Cell(30,10,$this->title,0,0,'C');
            //Line break
            $this->Ln(26);
        }

        //Page footer
        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->Cell(0,10,'Brought to you by the XLC team',0,2,'C');

            $this->SetY(-10);

            //Arial italic 8
            $this->SetFont('Arial','I',8);
            //Page number
            $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
        }
}

?>
