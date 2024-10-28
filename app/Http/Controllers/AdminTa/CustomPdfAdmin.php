<?php

namespace App\Http\Controllers\AdminTa;

use setasign\Fpdi\Fpdi;

class CustomPdfAdmin extends Fpdi
{
    protected $widths;
    protected $aligns;

    function SetWidths($w)
    {
        $this->widths = $w;
    }

    function Row($data, $underline = [])
    {
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 7 * $nb;
        $this->CheckPageBreak($h);
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h);
            $this->SetXY($x, $y + ($h - $this->NbLines($w, $data[$i]) * 7) / 2);

            // Check if $underline[$i] is set and not null
            if (isset($underline[$i]) && $underline[$i]) {
                // Split text to get the position for underlining
                $text = explode("\n", $data[$i]);
                if (count($text) > 1) {
                    // Draw "SURAT TUGAS" and underline it
                    $this->MultiCell($w, 7, $text[0], 0, $a);
                    $textWidth = $this->GetStringWidth($text[0]);
                    $this->Line($x + ($w - $textWidth) / 2, $this->GetY() - 1, $x + ($w - $textWidth) / 2 + $textWidth, $this->GetY() - 1); // Draw underline
                    $this->SetXY($x, $this->GetY() + 1); // Move cursor down for the next line

                    // Draw "No.xxxx"
                    $this->MultiCell($w, 6, $text[1], 0, $a);
                } else {
                    $this->MultiCell($w, 7, $data[$i], 0, $a);
                    $textWidth = $this->GetStringWidth($data[$i]);
                    $this->Line($x + ($w - $textWidth) / 2, $this->GetY() - 1, $x + ($w - $textWidth) / 2 + $textWidth, $this->GetY() - 1); // Draw underline
                }
            } else {
                $this->MultiCell($w, 7, $data[$i], 0, $a);
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
