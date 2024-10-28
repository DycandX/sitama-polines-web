<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class NilaiHelper
{
    public static function nilaiDeskripsi($nilai)
    {
        $nilai = floor($nilai); // Membulatkan nilai ke bawah ke bilangan bulat terdekat
        if ($nilai >= 80 && $nilai <= 100) {
            return 'A';
        } elseif ($nilai >= 75 && $nilai <= 79) {
            return 'AB';
        } elseif ($nilai >= 70 && $nilai <= 74) {
            return 'B';
        } elseif ($nilai >= 66 && $nilai <= 69) {
            return 'BC';
        } elseif ($nilai >= 60 && $nilai <= 65) {
            return 'C';
        } elseif ($nilai >= 40 && $nilai <= 59) {
            return 'D';
        } elseif ($nilai >= 1 && $nilai <= 39) {
            return 'E';
        } else {
            return '-';
        }
    }

    public static function formatNilai($nilai)
    {
        return $nilai == floor($nilai) ? number_format($nilai, 0) : number_format($nilai, 2);
    }

    public static function nim2kelas($nim)
    {
        $temp = '';
        $depan_jenjang = substr($nim, 0, 1);
        if ($depan_jenjang == '3') {
            $temp .= 'IK-3';
        } elseif ($depan_jenjang == '4') {
            $temp .= 'TI-4';
        }
        $depan_kelas = substr($nim, 5, 1);
        if ($depan_kelas == 0) {
            $temp .= 'A';
        } elseif ($depan_kelas == 1) {
            $temp .= 'B';
        } elseif ($depan_kelas == 2) {
            $temp .= 'C';
        } elseif ($depan_kelas == 3) {
            $temp .= 'D';
        } elseif ($depan_kelas == 4) {
            $temp .= 'E';
        } elseif ($depan_kelas == 5) {
            $temp .= 'F';
        } elseif ($depan_kelas == 6) {
            $temp .= 'G';
        } elseif ($depan_kelas == 7) {
            $temp .= 'H';
        }

        return $temp;
    }
}
