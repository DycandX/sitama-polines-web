<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BimbinganLog extends Model
{
    use HasFactory;

    protected $table = "bimbingan_log";

    protected $primaryKey = "bimbingan_log_id";

    protected $fillable = [
        'bimbingan_id',
        'bimb_tgl',
        'bimb_judul',
        'bimb_desc',
        'bimb_file_original',
        'bimb_file'
    ];

    public $timestamps = false;

    // Admin
    public static function log()
    {
        $log = DB::table('bimbingan_log')
            ->join('bimbingans', 'bimbingans.bimbingan_id', '=', 'bimbingan_log.bimbingan_id')
            ->join('dosen', 'dosen.dosen_nip', '=', 'bimbingans.dosen_nip')
            ->select(
                'bimbingan_log.bimbingan_id',
                'bimbingan_log.bimb_tgl',
                'bimbingan_log.bimb_judul',
                'bimbingan_log.bimb_desc',
                'bimbingan_log.bimb_file_original',
                'bimbingan_log.bimb_file',
                'bimbingan_log.bimb_status',
                'bimbingans.ta_id',
                'bimbingans.dosen_nip',
                'bimbingans.urutan',
                'dosen.dosen_nama'
            )
            ->orderBy('bimbingan_log.bimbingan_log_id', 'asc')
            ->get();
        // dd($log);
        return $log;
    }

    // Mahasiswa
    public static function BimbinganLog($id)
    {

        $log = DB::table('bimbingan_log')
            ->join('bimbingans', 'bimbingan_log.bimbingan_id', '=', 'bimbingans.bimbingan_id')
            ->join('tas', 'tas.ta_id', '=', 'bimbingans.ta_id')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
            ->join('users', 'users.email', '=', 'mahasiswa.email')
            ->join('dosen', 'bimbingans.dosen_nip', '=', 'dosen.dosen_nip')
            ->where('users.id', '=', $id)
            ->orderBy('bimbingan_log.bimb_tgl', 'desc')
            ->get();
        return $log;
    }

    public static function batasEdit($id)
    {
        $log = DB::table('bimbingan_log')
            ->join('bimbingans', 'bimbingans.bimbingan_id', '=', 'bimbingan_log.bimbingan_id')
            ->where('bimbingan_log.bimbingan_log_id', $id)
            ->first();
        return $log;
    }

    // Dosen
    public static function bimbinganLogDosen($ta_id)
    {
        $email = Auth::user()->email;

        $log = DB::table('bimbingan_log')
            ->join('bimbingans', 'bimbingan_log.bimbingan_id', '=', 'bimbingans.bimbingan_id')
            ->join('tas', 'tas.ta_id', '=', 'bimbingans.ta_id')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
            ->join('dosen', 'bimbingans.dosen_nip', '=', 'dosen.dosen_nip')
            ->where('tas.ta_id', '=', $ta_id)
            ->where('dosen.email', $email)
            ->select('bimbingan_log.*', 'mahasiswa.mhs_nim', 'bimbingans.urutan') // Include mhs_nim in the results
            ->get();
        return $log;
    }
}
