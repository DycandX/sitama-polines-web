<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Session;

class Penilian extends Model
{
    use HasFactory;

    public static function nilai_akhirmhs(){

        $email = Auth::user()->email;
        $query = DB::table('seminar_magangs')
                ->join('magangs', 'magangs.magang_id', '=', 'seminar_magangs.magang_id')
                ->join('magang_industris', 'magang_industris.magang_id', '=', 'magangs.magang_id')
                ->join('dosen', 'dosen.dosen_nip', '=', 'magangs.dosen_nip')
                ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
                ->join('master_ta', 'master_ta.ta', '=', 'magangs.ta')
                ->join('users', 'users.email', '=', 'dosen.email')
                ->select(
                'seminar_magangs.nilai_akhir',
                'seminar_magangs.nilai_dosen',
                'seminar_magangs.nilai_industri', 
                'magangs.magang_id','mahasiswa.mhs_nim', 
                'mahasiswa.mhs_nama', 
                'magang_industris.magang_id',
                DB::raw('GROUP_CONCAT(DISTINCT magang_industris.tgl_mulai) as tgl_mulai'),
                )
                ->where('users.email', $email)
                ->where('master_ta.status', 1)
                ->groupBY('seminar_magangs.nilai_akhir',
                'seminar_magangs.nilai_dosen',
                'seminar_magangs.nilai_industri', 
                'magangs.magang_id','mahasiswa.mhs_nim', 
                'mahasiswa.mhs_nama', 
                'magang_industris.magang_id',)
                ->get();
                $coba = $query->map(function ($item) {
                    $item->tgl_mulai = explode('|', $item->tgl_mulai);
                    return $item;
                }); 

        return $coba;
    }

    public static function nilai_mhs(){
        
        $query = DB::table('penilians')
        ->join('magangs', 'magangs.magang_id', '=', 'penilians.magang_id')
        ->join('komponen_penilian_detail', 'komponen_penilian_detail.komponen_detail_id', '=', 'penilians.komponen_detail_id')
        ->join('komponen_penilian', 'komponen_penilian.komponen_id', '=', 'komponen_penilian_detail.komponen_id')
        ->select('komponen_penilian_detail.komponen_detail_id', 'komponen_penilian_detail.keterangan','penilians.*', 'komponen_penilian.*', 'magangs.magang_id' )
        ->where('komponen_penilian.jenis_penilian', 0);
        

        return $query->get(); 
       
    }
    public static function komponen_nilai() {
        $query = DB::table('komponen_penilian')
            ->join('komponen_penilian_detail', 'komponen_penilian_detail.komponen_id', '=', 'komponen_penilian.komponen_id')
            ->selectRaw('komponen_penilian_detail.komponen_detail_id, komponen_penilian.*, komponen_penilian_detail.keterangan')
            ->where('komponen_penilian.jenis_penilian', 0);
    
        return $query->get();
    }

    public static function nilaindustri($magang_id = null) {
        $query = DB::table('magang_industris')
            ->join('magangs', 'magangs.magang_id', '=', 'magang_industris.magang_id')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
            ->join('industris', 'industris.industri_id', '=', 'magang_industris.industri_id')
            ->select('magangs.magang_id', 'magang_industris.*', 'mahasiswa.mhs_nim', 'mahasiswa.mhs_nama', 'industris.nama_industri');
    
        if ($magang_id) {
            $query->where('magangs.magang_id', $magang_id);
        }
    
        return $query->get();
    }


    public static function nilaimhsss(){

        $email = Auth::user()->email;
        $query = DB::table('seminar_magangs')
                ->join('magangs', 'magangs.magang_id', '=', 'seminar_magangs.magang_id')
                ->join('magang_industris', 'magang_industris.magang_id', '=', 'magangs.magang_id')
                ->join('dosen', 'dosen.dosen_nip', '=', 'magangs.dosen_nip')
                ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
                ->join('users', 'users.email', '=', 'mahasiswa.email')
                ->select(
                'seminar_magangs.nilai_akhir',
                'seminar_magangs.nilai_dosen',
                'seminar_magangs.nilai_industri', 
                'magangs.magang_id',
                'mahasiswa.mhs_nim', 
                'mahasiswa.mhs_nama', 
                'magang_industris.magang_id',
                DB::raw('GROUP_CONCAT(DISTINCT magang_industris.tgl_mulai) as tgl_mulai'),
                )
                ->where('users.email', $email)
                ->groupBY('seminar_magangs.nilai_akhir',
                'seminar_magangs.nilai_dosen',
                'seminar_magangs.nilai_industri', 
                'magangs.magang_id',
                'mahasiswa.mhs_nim', 
                'mahasiswa.mhs_nama', 
                'magang_industris.magang_id',)
                ->get();
                $coba = $query->map(function ($item) {
                    $item->tgl_mulai = explode('|', $item->tgl_mulai);
                    return $item;
                }); 
        return $coba;
    }

    public static function komponenmhs(){

        $email = Auth::user()->email;
        $query = DB::table('penilians')
        ->join('magangs', 'magangs.magang_id', '=', 'penilians.magang_id')
        ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'magangs.mhs_nim')
        ->join('users', 'users.email', '=', 'mahasiswa.email')
        ->join('komponen_penilian_detail', 'komponen_penilian_detail.komponen_detail_id', '=', 'penilians.komponen_detail_id')
        ->join('komponen_penilian', 'komponen_penilian.komponen_id', '=', 'komponen_penilian_detail.komponen_id')
        ->select('komponen_penilian_detail.komponen_detail_id', 'komponen_penilian_detail.keterangan','penilians.*', 'komponen_penilian.*', 'magangs.magang_id' )
        ->where('users.email', $email)
        ->get(); 

        

        return $query;
       
    }

    public static function nilaiperkomponen($magang_id = null) {
        $query = DB::table('penilians')
            ->join('magangs', 'magangs.magang_id', '=', 'penilians.magang_id')
            ->join('komponen_penilian_detail', 'komponen_penilian_detail.komponen_detail_id', '=', 'penilians.komponen_detail_id')
            ->join('komponen_penilian', 'komponen_penilian.komponen_id', '=', 'komponen_penilian_detail.komponen_id')
            ->select('komponen_penilian_detail.komponen_detail_id', 'komponen_penilian_detail.keterangan','penilians.*', 'komponen_penilian.*', 'magangs.magang_id' );
        
    
        if ($magang_id) {
            $query->where('magangs.magang_id', $magang_id);
        }
    
        return $query->get();
    }

    
    

    
    
    




}

