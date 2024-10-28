<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MahasiswaBimbingan extends Model
{
    use HasFactory;

    protected $table = 'bimbingans';
    protected $primaryKey = 'bimbingan_id';

    public static function ta_mahasiswa()
    {
        $email = Auth::user()->email;
        $dosen = DB::select("SELECT * FROM dosen WHERE email='$email';");
        //$dosen = DB::table('dosen')->where('email', $email)->first();

        $ta_mahasiswa = DB::table('tas')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
            ->join('kode_prodi', 'kode_prodi.prodi_ID', '=', 'mahasiswa.prodi_ID')
            ->join('bimbingans', 'bimbingans.ta_id', '=', 'tas.ta_id')
            ->leftJoin('bimbingan_log', 'bimbingans.bimbingan_id', '=', 'bimbingan_log.bimbingan_id')
            ->join('dosen', 'dosen.dosen_nip', '=', 'bimbingans.dosen_nip')
            ->where('dosen.email', $email)
            ->where('bimbingans.dosen_nip', $dosen[0]->dosen_nip)
            //->where('bimbingans.dosen_nip', $dosen->dosen_nip)
            ->select(
                'tas.ta_id',
                'tas.ta_judul',
                'tas.tahun_akademik',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'mahasiswa.prodi_ID',
                'kode_prodi.program_studi',
                'bimbingans.dosen_nip',
                'dosen.dosen_nama',
                'bimbingans.urutan',
                'bimbingans.verified',
                DB::raw('SUM(CASE WHEN bimbingan_log.bimb_status = 1 THEN 1 ELSE 0 END) as jml_bimbingan_valid'),
                DB::raw('SUM(CASE WHEN bimbingan_log.bimb_status = 0 THEN 1 ELSE 0 END) as jml_bimbingan_invalid')
            )
            ->orderBy('mahasiswa.mhs_nim', 'asc')
            ->groupBy(
                'tas.ta_id',
                'tas.ta_judul',
                'tas.tahun_akademik',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'mahasiswa.prodi_ID',
                'kode_prodi.program_studi',
                'bimbingans.dosen_nip',
                'dosen.dosen_nama',
                'bimbingans.urutan',
                'bimbingans.verified'
            )
            ->get();

        return $ta_mahasiswa;
    }
}
