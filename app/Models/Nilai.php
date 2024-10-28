<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Nilai extends Model
{

    public static function penilaian($ta_sidang_id)
    {
        $penilaian = DB::table('ta_sidang')
            ->leftJoin('tas', 'tas.ta_id', '=', 'ta_sidang.ta_id')
            ->leftJoin('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
            ->select(
                'ta_sidang.ta_sidang_id',
                'tas.ta_id',
                'tas.tahun_akademik',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'ta_sidang.nilai_akhir',
                'ta_sidang.nilai_pembimbing',
                'ta_sidang.nilai_penguji',
                'ta_sidang.status_lulus',
            )
            ->where('ta_sidang.ta_sidang_id', $ta_sidang_id)
            ->orderBy('mahasiswa.mhs_nim', 'asc')
            ->first();

        return $penilaian;
    }

    public static function penguji($ta_sidang_id)
    {
        $penguji = DB::table('ta_sidang')
            ->leftJoin('tas', 'tas.ta_id', '=', 'ta_sidang.ta_id')
            ->leftJoin('penilaian_penguji', 'penilaian_penguji.ta_sidang_id', '=', 'ta_sidang.ta_sidang_id')
            ->leftJoin('penilaian_penguji_detail', function ($join) {
                $join->on('penilaian_penguji_detail.ta_sidang_id', '=', 'penilaian_penguji.ta_sidang_id');
                $join->on('penilaian_penguji_detail.dosen_nip', '=', 'penilaian_penguji.dosen_nip');
            })
            ->leftJoin('unsur_nilai_penguji', 'unsur_nilai_penguji.nilai_id', '=', 'penilaian_penguji_detail.nilai_id')
            ->leftJoin('dosen as dosen_penguji', 'dosen_penguji.dosen_nip', '=', 'penilaian_penguji.dosen_nip')
            ->select(
                'penilaian_penguji.urutan',
                'dosen_penguji.dosen_nip',
                'dosen_penguji.dosen_nama',
                'unsur_nilai_penguji.unsur_nilai',
                'unsur_nilai_penguji.bobot',
                'penilaian_penguji_detail.berinilai',
                'penilaian_penguji_detail.nilai_id',
                'ta_sidang.nilai_akhir'
            )
            ->where('ta_sidang.ta_sidang_id', $ta_sidang_id)
            ->orderBy('penilaian_penguji.urutan')
            ->orderBy('penilaian_penguji_detail.penilaian_id')
            ->get();
        // ($penguji);
        $groupedPenguji = [];
        foreach ($penguji as $item) {
            $key = $item->urutan . '-' . $item->dosen_nama . '-' . $item->nilai_id . '-' . $item->bobot;
            if (!isset($groupedPenguji[$key])) {
                $total = $item->berinilai * $item->bobot;
                $groupedPenguji[$key] = (object) [
                    'urutan' => $item->urutan,
                    'dosen_nip' => $item->dosen_nip,
                    'dosen_nama' => $item->dosen_nama,
                    'unsur_nilai' => $item->unsur_nilai,
                    'berinilai' => $item->berinilai,
                    'bobot' => $item->bobot,
                    'total' => $total
                ];
            }
        }

        return array_values($groupedPenguji);
    }

    public static function pembimbing($ta_sidang_id)
    {
        $pembimbing = DB::table('ta_sidang')
            ->leftJoin('tas', 'tas.ta_id', '=', 'ta_sidang.ta_id')
            ->leftJoin('bimbingans', 'bimbingans.ta_id', '=', 'ta_sidang.ta_id')
            ->leftJoin('penilaian_pembimbing', function ($join) {
                $join->on('penilaian_pembimbing.ta_sidang_id', '=', 'ta_sidang.ta_sidang_id');
                $join->on('penilaian_pembimbing.dosen_nip', '=', 'bimbingans.dosen_nip');
            })
            ->leftJoin('unsur_nilai_pembimbing', 'unsur_nilai_pembimbing.nilai_id', '=', 'penilaian_pembimbing.nilai_id')
            ->leftJoin('dosen as dosen_pembimbing', 'dosen_pembimbing.dosen_nip', '=', 'bimbingans.dosen_nip')
            ->select(
                'bimbingans.urutan',
                'dosen_pembimbing.dosen_nip',
                'dosen_pembimbing.dosen_nama',
                'unsur_nilai_pembimbing.unsur_nilai',
                'unsur_nilai_pembimbing.bobot',
                'penilaian_pembimbing.berinilai',
                'penilaian_pembimbing.nilai_id'
            )
            ->where('ta_sidang.ta_sidang_id', $ta_sidang_id)
            ->orderBy('bimbingans.urutan')
            ->orderBy('penilaian_pembimbing.penilaian_id')
            ->get();

        $groupedPembimbing = [];
        foreach ($pembimbing as $item) {
            $key = $item->urutan . '-' . $item->dosen_nama . '-' . $item->nilai_id . '-' . $item->bobot;
            if (!isset($groupedPembimbing[$key])) {
                $total = $item->berinilai * $item->bobot;
                $groupedPembimbing[$key] = (object) [
                    'urutan' => $item->urutan,
                    'dosen_nip' => $item->dosen_nip,
                    'dosen_nama' => $item->dosen_nama,
                    'unsur_nilai' => $item->unsur_nilai,
                    'berinilai' => $item->berinilai,
                    'bobot' => $item->bobot,
                    'total' => $total
                ];
            }
        }
        // dd($groupedPembimbing);
        return array_values($groupedPembimbing);
    }
}
