<?php

namespace App\Http\Controllers\API\DosenTa;

use setasign\Fpdi\Fpdi;

class CustomPdfRekapNilai extends Fpdi
{
    protected $widths;
    protected $aligns;

    function SetWidths($w)
    {
        // Set the array of column widths
        $this->widths = $w;
    }

    function Row($data, $rowHeight = 10)
    {
        // Calculate the maximum number of lines in the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }
        // Set the height based on the number of lines or use a fixed height
        $h = max($rowHeight, 7 * $nb);

        // Issue a page break if needed
        $this->CheckPageBreak($h);

        // Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

            // Save the current position
            $x = $this->GetX();
            $y = $this->GetY();

            // Draw the border
            $this->Rect($x, $y, $w, $h);

            // Print the text or image with vertical alignment
            if ($i == count($data) - 1 && (strpos($data[$i], '.png') !== false || strpos($data[$i], '.jpg') !== false || strpos($data[$i], '.jpeg') !== false)) {
                $filePath = public_path('dist/img/' . $data[$i]);
                if (file_exists($filePath)) {
                    // Adjust the image size and position to fit within the cell
                    $imgWidth = min($w * 0.40, $w - 2); // Ensuring the image fits within 3/4 of the cell width
                    $imgHeight = $imgWidth * 0.75; // Maintain the aspect ratio (example)
                    $imgX = $x + ($w - $imgWidth) / 2; // Center the image in the cell
                    $imgY = $y + ($h - $imgHeight) / 2; // Center the image vertically

                    $this->Image($filePath, $imgX, $imgY, $imgWidth, $imgHeight);
                } else {
                    $this->MultiCell($w, 7, 'Signature not found', 0, 'C');
                }
            } else {
                $this->MultiCell($w, 7, $data[$i], 0, $a);
            }

            // Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }

        // Go to the next line
        $this->Ln($h);
    }


    function CheckPageBreak($h)
    {
        // If the height h would cause an overflow, add a new page immediately
        if($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        // Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if($nb > 0 and $s[$nb-1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while($i < $nb)
        {
            $c = $s[$i];
            if($c == "\n")
            {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if($l > $wmax)
            {
                if($sep == -1)
                {
                    if($i == $j)
                        $i++;
                }
                else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
}
