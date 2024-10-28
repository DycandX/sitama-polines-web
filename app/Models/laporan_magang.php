<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Session;

class Laporan_magang extends Model
{
    use HasFactory;
    protected $table = 'laporan_magangs';
    protected $primaryKey = 'laporan_id';
    protected $fillable = ['magang_judul','file_magang_original', 'file_magang'];

    public $incrementing = true;


    public static function laporan_mhs($magang_id = null)
    {

        $query = DB::table('laporan_magangs')
            ->join('magangs', 'magangs.magang_id', '=', 'laporan_magangs.magang_id')
            ->select('magangs.magang_id', 'laporan_magangs.*');

        // Jika parameter magang_id diberikan, tambahkan klausa 'where'

        if ($magang_id) {
            $query->where('magangs.magang_id', $magang_id);
        }

        return $query->get(); // Ambil data
    }

    public static function TambahDokumen()
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
        $TambahDokumen = DB::table('laporan_magangs')
            ->join('magangs', 'magangs.magang_id', '=', 'laporan_magangs.magang_id')
            ->select('magangs.magang_id', 'laporan_magangs.*')
            ->where('magangs.magang_id', $magang_id)
            ->get();
            // dd($TambahDokumen);

        return $TambahDokumen;
    }
}
