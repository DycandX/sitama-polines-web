<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class bimbingan_magang extends Model
{
    use HasFactory;
    protected $table = 'bimbingan_magangs';
    protected $primaryKey = 'bimbingan_magang_id';
    public $timestamps = false; 
    public static function bimbingan_mhs($magang_id = null){
        
        $query = DB::table('bimbingan_magangs')
        ->join('magangs', 'magangs.magang_id', '=', 'bimbingan_magangs.magang_id')
        ->select('magangs.magang_id', 'bimbingan_magangs.*');

       // Jika parameter magang_id diberikan, tambahkan klausa 'where'
        if ($magang_id) {
                $query->where('magangs.magang_id', $magang_id);
        }

        return $query->get(); // Ambil data
    }
}
