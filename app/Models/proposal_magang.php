<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class proposal_magang extends Model
{
    use HasFactory;

    protected $primaryKey = 'proposal_id';
    // const CREATED_AT = 'created_at';  // Nama kolom untuk tanggal pembuatan
    protected $table = 'proposal_magangs';
    public $timestamps = false;

    public static function proposal_mhs($magang_id = null){
        
        $query = DB::table('proposal_magangs')
        ->join('magangs', 'magangs.magang_id', '=', 'proposal_magangs.magang_id')
        ->select('magangs.magang_id', 'proposal_magangs.*');

       // Jika parameter magang_id diberikan, tambahkan klausa 'where'
        if ($magang_id) {
                $query->where('magangs.magang_id', $magang_id);
        }

        return $query->get(); // Ambil data
    }
    

    
}
