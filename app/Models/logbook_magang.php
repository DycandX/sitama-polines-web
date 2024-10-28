<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Session;

class logbook_magang extends Model
{
    use HasFactory;
    protected $table = 'logbook_magangs';
    protected $primaryKey = 'logbook_id';
    public $timestamps = false;
    
    public $incrementing = true;
    public static function LogbookByMagang()
    {
        // Mendapatkan email pengguna yang sedang terautentikasi
        $email = Auth::user()->email;

        // Mendapatkan magang_id dari tabel magangs berdasarkan email pengguna
        $magang_id = DB::table('magangs')
            ->join('mahasiswa', 'magangs.mhs_nim', '=', 'mahasiswa.mhs_nim')
            ->join('users', 'mahasiswa.email', '=', 'users.email')
            ->where('users.email', $email)
            ->value('magangs.magang_id');

        // Simpan magang_id ke dalam session
        session(['magang_id' => $magang_id]);

        // Mengambil data logbook_magangs berdasarkan magang_id dari session
        $logbookMagangs = DB::table('logbook_magangs')
            ->join('magangs', 'magangs.magang_id', '=', 'logbook_magangs.magang_id')
            ->select('magangs.magang_id', 'logbook_magangs.*')
            ->where('magangs.magang_id', $magang_id)
            ->get();

        return $logbookMagangs;
    }
}