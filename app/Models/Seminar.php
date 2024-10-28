<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class Seminar extends Model
{
    
    protected $primaryKey = 'seminar_id';
    public $timestamps = false;
    protected $table = 'seminar_magangs';
    protected $fillable = [
        'mhs_nim', 'tgl_seminar', 'status_seminar', 'waktu', 'ruangan_nama'
    ];
    // protected $attributes = [
    //     'status_seminar' => '0', // Nilai default untuk kolom status_seminar
    // ];

    public function magang()
    {
        return $this->belongsTo(Magang::class, 'magang_id', 'magang_id');
    }


    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mhs_nim', 'mhs_nim');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id', 'ruangan_id');
    }

    public static function data()
    {
        $data = DB::table('seminar_magangs')
            ->join('magangs', 'magangs.magang_id', '=', 'seminar_magangs.magang_id')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->leftjoin('ruangan_ta', 'ruangan_ta.ruangan_id', '=', 'seminar_magangs.ruangan_id')
            ->select(
                'magangs.mhs_nim',
                'magangs.magang_id',
                'mahasiswa.mhs_nama',
                'seminar_magangs.seminar_id', // Gunakan mhs_nama dari tabel magangs
                'seminar_magangs.tgl_seminar',
                'seminar_magangs.status_seminar',
                'seminar_magangs.waktu',
                'ruangan_ta.ruangan_nama',
                'seminar_magangs.tgl_seminar',
                'seminar_magangs.status_seminar',
                'seminar_magangs.waktu',
                'seminar_magangs.nilai_dosen',
                'seminar_magangs.nilai_industri',
                'seminar_magangs.nilai_akhir',
            )
            ->groupBy(
                'magangs.magang_id',
                'magangs.mhs_nim',
                'mahasiswa.mhs_nama',
                'seminar_magangs.seminar_id', // Gunakan mhs_nama dari tabel magangs
                'seminar_magangs.tgl_seminar',
                'seminar_magangs.status_seminar',
                'seminar_magangs.waktu',
                'ruangan_ta.ruangan_nama',
                'seminar_magangs.tgl_seminar',
                'seminar_magangs.status_seminar',
                'seminar_magangs.waktu',
                'seminar_magangs.nilai_dosen',
                'seminar_magangs.nilai_industri',
                'seminar_magangs.nilai_akhir',
            )
            ->get();

        return $data;
    }

    public static function selesai()
    {
        $selesai = DB::table('seminar_magangs')
            ->join('magangs', 'magangs.magang_id', '=', 'seminar_magangs.magang_id')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->join('ruangan_ta', 'ruangan_ta.ruangan_id', '=', 'seminar_magangs.ruangan_id')
            ->select(
                'magangs.mhs_nim',
                'mahasiswa.mhs_nama', // Gunakan mhs_nama dari tabel magangs
                'seminar_magangs.tgl_seminar',
                'seminar_magangs.status_seminar',
                'seminar_magangs.waktu',
                'ruangan_ta.ruangan_nama',
                'seminar_magangs.*'
            )
            ->get();

        return $selesai;
    }

    public static function daftarmhs()
    {
        $validasi = DB::table('validasi_magangs')
            ->join('magangs', 'magangs.magang_id', '=', 'validasi_magangs.magang_id')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->select(
                'magangs.magang_id',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'validasi_magangs.*'
            )
            ->whereNotIn('magangs.magang_id', function($query) {
                $query->select('magang_id')
                      ->from('seminar_magangs');
            })
            ->get();
            
        return $validasi;
    }


    public static function seminarmhs()
    {   
        $email = Auth::user()->email;
        $data = DB::table('seminar_magangs')
            ->join('magangs', 'magangs.magang_id', '=', 'seminar_magangs.magang_id')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->join('users', 'users.email', '=', 'mahasiswa.email')
            ->leftjoin('ruangan_ta', 'ruangan_ta.ruangan_id', '=', 'seminar_magangs.ruangan_id')
            ->select(
                'magangs.mhs_nim',
                'mahasiswa.mhs_nama', // Gunakan mhs_nama dari tabel magangs
                'seminar_magangs.tgl_seminar',
                'seminar_magangs.status_seminar',
                'seminar_magangs.waktu',
                'ruangan_ta.ruangan_nama'
            )
            ->where('users.email', $email)
            ->get();

        return $data;
    }
    
}
