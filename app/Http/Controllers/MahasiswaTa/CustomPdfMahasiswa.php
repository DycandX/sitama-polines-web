<?php
// CustomPdfMahasiswa.php

namespace App\Http\Controllers\MahasiswaTa;

use setasign\Fpdi\Fpdi;

class CustomPdfMahasiswa extends Fpdi
{
    protected $widths;
    protected $aligns;

    function SetWidths($w)
    {
        $this->widths = $w;
    }

    function Row($data, $underline = [], $images = [])
    {
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }
        $h = 6 * $nb;
        $this->CheckPageBreak($h);
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = 'L';  // Set alignment to 'L' for left alignment
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h);
            $this->SetXY($x, $y);

            // Check for images
            if (isset($images[$i]) && $images[$i]) {
                // Get original image size
                list($originalWidth, $originalHeight) = getimagesize($images[$i]);

                // Maximum dimensions for the image
                $maxWidth = $w - 10; // Adjust as needed
                $maxHeight = $h - 10; // Adjust as needed

                // Calculate aspect ratio
                $aspectRatio = $originalWidth / $originalHeight;

                // Calculate new dimensions while maintaining aspect ratio
                if ($maxWidth / $aspectRatio <= $maxHeight) {
                    $newWidth = $maxWidth;
                    $newHeight = $maxWidth / $aspectRatio;
                } else {
                    $newHeight = $maxHeight;
                    $newWidth = $maxHeight * $aspectRatio;
                }

                // Set a minimum size for the signature
                $minSize = 10; // Minimum height for the image
                if ($newHeight < $minSize) {
                    $newHeight = $minSize;
                    $newWidth = $minSize * $aspectRatio; // Adjust width to maintain aspect ratio
                }

                // Add the image with the calculated dimensions
                $this->Image($images[$i], $x + 2, $y + 2, $newWidth, $newHeight);
            } else {
                $text = explode("\n", $data[$i]);
                foreach ($text as $line) {
                    $this->MultiCell($w, 6, $line, 0, $a);
                    $this->SetXY($x, $this->GetY()); // Ensure no additional space
                }
            }

            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h);
    }




    function CheckPageBreak($h)
    {
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

?>