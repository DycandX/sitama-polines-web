<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Session;

class Magang extends Model
{
    use HasFactory;
    protected $table = 'magangs';
    public static function magang_mhs($tahun_akademik)
    {

        $email = Auth::user()->email;
        $magang_mhs = DB::table('magangs')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->join('dosen', 'dosen.dosen_nip', '=', 'magangs.dosen_nip')
            ->join('magang_industris', 'magang_industris.magang_id', '=', 'magangs.magang_id')
            ->join('industris', 'industris.industri_id', '=', 'magang_industris.industri_id')
            ->leftjoin('validasi_magangs', 'validasi_magangs.magang_id', '=', 'magangs.magang_id')
            ->join('users', 'users.email', '=', 'dosen.email')
            ->select(
            'mahasiswa.mhs_nim', 
            'dosen.dosen_nip', 
            'mahasiswa.mhs_nama', 
            'dosen.dosen_nama', 
            'dosen.email', 
            'magangs.magang_id', 
            'magangs.ta',
            'validasi_magangs.validasi_id',
            DB::raw('GROUP_CONCAT(DISTINCT industris.nama_industri) as nama_industri'),
            DB::raw('GROUP_CONCAT(DISTINCT magang_industris.tgl_mulai) as tgl_mulai'),
            DB::raw('GROUP_CONCAT(DISTINCT magang_industris.tgl_selesai) as tgl_selesai'),
            )
            ->where('users.email', $email)
            ->where('magangs.ta', $tahun_akademik)
            ->groupBY('mahasiswa.mhs_nim', 
            'dosen.dosen_nip', 
            'mahasiswa.mhs_nama', 
            'dosen.dosen_nama', 
            'dosen.email', 
            'magangs.magang_id', 
            'magangs.ta',
            'validasi_magangs.validasi_id',)
            ->get();

            $coba = $magang_mhs->map(function ($item) {
                $item->nama_industri = explode(',', $item->nama_industri);
                $item->tgl_mulai = explode(',', $item->tgl_mulai);
                $item->tgl_selesai = explode(',', $item->tgl_selesai);
                return $item;
            }); 

            
        return $coba;
    }

    use HasFactory;
    public static function logbook_mhs($magang_id = null)
    {

        $query = DB::table('logbook_magangs')
            ->join('magangs', 'magangs.magang_id', '=', 'logbook_magangs.magang_id')
            ->select('magangs.magang_id', 'logbook_magangs.*');

        // Jika parameter magang_id diberikan, tambahkan klausa 'where'
        if ($magang_id) {
            $query->where('magangs.magang_id', $magang_id);
        }

        return $query->get(); // Ambil data
    }

    public static function validasi_mhs($magang_id = null)
    {

        $query = DB::table('validasi_magangs')
            ->join('magangs', 'magangs.magang_id', '=', 'validasi_magangs.magang_id')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->select('magangs.magang_id', 'mahasiswa.mhs_nama', 'mahasiswa.mhs_nim',);

        // Jika parameter magang_id diberikan, tambahkan klausa 'where'
        if ($magang_id) {
            $query->where('magangs.magang_id', $magang_id);
        }

        return $query->get(); // Ambil data
    }

    public static function data($tahun_akademik)
    {
        $data = DB::table('magangs')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->leftjoin('dosen', 'dosen.dosen_nip', '=', 'magangs.dosen_nip')
            ->join('magang_industris', 'magang_industris.magang_id', '=', 'magangs.magang_id')
            ->join('industris', 'industris.industri_id', '=', 'magang_industris.industri_id')
            ->leftjoin('seminar_magangs', 'seminar_magangs.magang_id', '=', 'magangs.magang_id') // tambahkan '=' yang hilang
            ->select(
                'mahasiswa.mhs_nama',
                'mahasiswa.mhs_nim',
                'dosen.dosen_nama',
                'dosen.dosen_nip',
                'magangs.magang_id',
                'seminar_magangs.nilai_akhir',
                'magangs.ta',
                DB::raw('GROUP_CONCAT(DISTINCT industris.nama_industri) as nama_industri'),
                DB::raw('GROUP_CONCAT(DISTINCT magang_industris.tgl_mulai) as tgl_mulai'),
                DB::raw('GROUP_CONCAT(DISTINCT magang_industris.tgl_selesai) as tgl_selesai'),
            )
            ->where('magangs.ta', $tahun_akademik)
            ->groupBY(
                'mahasiswa.mhs_nama',
                'mahasiswa.mhs_nim',
                'dosen.dosen_nama',
                'dosen.dosen_nip',
                'magangs.magang_id',
                'seminar_magangs.nilai_akhir',
                'magangs.ta'
            )
            ->get();
            $coba = $data->map(function ($item) {
                $item->nama_industri = explode(',', $item->nama_industri);
                $item->tgl_mulai = explode(',', $item->tgl_mulai);
                $item->tgl_selesai = explode(',', $item->tgl_selesai);
                return $item;
            }); 
        return $coba;
    }


    public static function nilai($magang_id = null)
    {

        $nilai = DB::table('seminar_magangs')
            ->join('magangs', 'magangs.magang_id', '=', 'seminar_magangs.magang_id')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->leftjoin('ruangan_ta', 'ruangan_ta.ruangan_id', '=', 'seminar_magangs.ruangan_id')
            ->select(
                'magangs.mhs_nim',
                'mahasiswa.mhs_nama', // Gunakan mhs_nama dari tabel magangs
                'seminar_magangs.tgl_seminar',
                'seminar_magangs.status_seminar',
                'seminar_magangs.waktu',
                'ruangan_ta.ruangan_nama',
                'seminar_magangs.*'
            );

        // Jika parameter magang_id diberikan, tambahkan klausa 'where'
        if ($magang_id) {
            $nilai->where('magangs.magang_id', $magang_id);
        }

        return $nilai->get(); // Ambil data
    }

    public static function magang($magang_id = null)
    {

        $query = DB::table('magangs')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->leftjoin('dosen', 'dosen.dosen_nip', '=', 'magangs.dosen_nip')
            ->select('magangs.magang_id', 'mahasiswa.mhs_nim', 'dosen.dosen_nip', 'dosen.dosen_nama', 'mahasiswa.mhs_nama');

        // Jika parameter magang_id diberikan, tambahkan klausa 'where'
        if ($magang_id) {
            $query->where('magangs.magang_id', $magang_id);
        }

        return $query->get(); // Ambil data
    }


    public static function validasimagangg($magang_id = null)
    {
        $query = DB::table('magangs')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->join('magang_industris', 'magang_industris.magang_id', '=', 'magangs.magang_id')
            ->join('industris', 'industris.industri_id', '=', 'magang_industris.industri_id')
            ->leftjoin('dosen', 'dosen.dosen_nip', '=', 'magangs.dosen_nip')
            ->select(
                'magangs.magang_id',
                'mahasiswa.mhs_nim', 
                'mahasiswa.mhs_nama', 
                DB::raw('GROUP_CONCAT(DISTINCT industris.nama_industri) as nama_industri')
            )
            ->groupBy('magangs.magang_id', 'mahasiswa.mhs_nim', 'mahasiswa.mhs_nama');

        // Jika parameter magang_id diberikan, tambahkan klausa 'where'
        if ($magang_id) {
            $query->where('magangs.magang_id', $magang_id);
        }

        $results = $query->get(); // Ambil data

        // Parsing nama_industri menjadi array jika Anda memilih
        $parsedResults = $results->map(function ($item) {
            $item->nama_industri = explode(',', $item->nama_industri);
            return $item;
        });

        return $parsedResults;
    }


    public static function daftarmhs()
    {

        $query = DB::table('magangs')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->select('magangs.magang_id', 'mahasiswa.mhs_nim', 'mahasiswa.mhs_nama');



        return $query->get(); // Ambil data
    }


    public static function BimbinganByMagang()
    {
        $id = Auth::user()->id;
        $magang = DB::table('magangs')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->leftJoin('dosen', 'dosen.dosen_nip', '=', 'magangs.dosen_nip')
            ->leftJoin('bimbingan_magangs', 'bimbingan_magangs.magang_id', '=', 'magangs.magang_id')
            ->leftJoin('magang_industris', 'magang_industris.magang_id', '=', 'magangs.magang_id')
            ->join('users', 'users.email', '=', 'mahasiswa.email')
            ->select('*')
            ->where('users.id', $id)
            ->first();

        return $magang;
    }

    public static function datamhs()
    {
        $email = Auth::user()->email;
        $query = DB::table('magang_industris')
            ->join('magangs', 'magangs.magang_id', '=', 'magang_industris.magang_id')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->join('industris', 'industris.industri_id', '=', 'magang_industris.industri_id')
            ->join('users', 'users.email', '=', 'mahasiswa.email')
            ->selectRaw('magangs.magang_id, magang_industris.*, mahasiswa.mhs_nim, mahasiswa.mhs_nama, industris.nama_industri')
            ->where('users.email', $email)
            ->get();

        return $query;
    }

    
    public static function datamhs22()
    {
        $email = Auth::user()->email;
        $query = DB::table('magangs')
            ->join('magang_industris', 'magang_industris.magang_id', '=', 'magangs.magang_id')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->join('industris', 'industris.industri_id', '=', 'magang_industris.industri_id')
            ->join('users', 'users.email', '=', 'mahasiswa.email')
            ->select(
            'magangs.magang_id', 
            'mahasiswa.mhs_nim', 
            'mahasiswa.mhs_nama', 
            DB::raw('GROUP_CONCAT(DISTINCT industris.nama_industri) as nama_industri'),
            DB::raw('GROUP_CONCAT(DISTINCT magang_industris.tgl_mulai) as tgl_mulai'),
            DB::raw('GROUP_CONCAT(DISTINCT magang_industris.tgl_selesai) as tgl_selesai'),

            )
            ->where('users.email', $email)
            ->groupBy('magangs.magang_id', 'mahasiswa.mhs_nim', 'mahasiswa.mhs_nama',)
            ->get();

        // Parsing nama_industri menjadi array jika Anda memilih
        $coba = $query->map(function ($item) {
        $item->nama_industri = explode(',', $item->nama_industri);
        $item->tgl_mulai = explode(',', $item->tgl_mulai);
        $item->tgl_selesai = explode(',', $item->tgl_selesai);
        return $item;
        }); 

        // dd($coba);

        return $coba;
    }


    public static function datadosen()
    {
        $email = Auth::user()->email;
        $query = DB::table('magangs')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->leftJoin('dosen', 'dosen.dosen_nip', '=', 'magangs.dosen_nip')
            ->join('users', 'users.email', '=', 'mahasiswa.email')
            ->selectRaw(
                'magangs.magang_id, 
                mahasiswa.mhs_nim, 
                mahasiswa.mhs_nama, 
                dosen.dosen_nip, 
                dosen.dosen_nama'
            )
            ->where('users.email', $email)
            ->get();

        return $query;
    }


    public static function BimbinganMHS()
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
        $bimbinganMagangs = DB::table('bimbingan_magangs')
            ->join('magangs', 'magangs.magang_id', '=', 'bimbingan_magangs.magang_id')
            ->select('magangs.magang_id', 'bimbingan_magangs.*')
            ->where('magangs.magang_id', $magang_id)
            ->get();

        return $bimbinganMagangs;
    }

    public static function magangId()
    {
        $id = Auth::user()->id;

        $magang_id = DB::table('magangs')
            ->join('mahasiswa', 'magangs.mhs_nim', '=', 'mahasiswa.mhs_nim')
            ->join('users', 'mahasiswa.email', '=', 'users.email')
            ->where('users.id', $id)
            ->select('magangs.magang_id')
            ->first();

        return $magang_id;
    }
    public static function syarat($magang_id = null)
    {

        $syarat = DB::table('laporan_magangs')
            ->join('magangs', 'magangs.magang_id', '=', 'laporan_magangs.magang_id')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->select(
                'magangs.magang_id',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'laporan_magangs.file_magang',
                'laporan_magangs.tipe'
            )
            ->groupBy(
                'magangs.magang_id',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'laporan_magangs.file_magang',
                'laporan_magangs.tipe',
            );

        // Jika parameter magang_id diberikan, tambahkan klausa 'where'
        if ($magang_id) {
            $syarat->where('magangs.magang_id', $magang_id);
        }

        return $syarat->get();  // Ambil data
    }

    public static function kota()
    {
        $kota = DB::table('reg_provinces')
            ->join('reg_regencies', 'reg_regencies.province_id', '=', 'reg_provinces.province_id')
            ->select('*');

        return $kota->get();
    }

    public static function Mhsmagang()
    {
        $id = Auth::user()->id;
        $magang = DB::table('mahasiswa')
            ->join('users', 'users.email', '=', 'mahasiswa.email')
            ->select('*')
            ->where('users.id', $id)
            ->first();

        return $magang;

    }
}
