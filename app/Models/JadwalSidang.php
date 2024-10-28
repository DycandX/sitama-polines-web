<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JadwalSidang extends Model
{
    use HasFactory;

    protected $table = "jadwal_sidang";

    protected $primaryKey = "jadwal_id";
    protected $fillable = [
        'tgl_sidang',
        'sesi_id',
        'ruangan_id'
    ];
    public $timestamps = false;

    public static function DaftarTa()
    {

        $log = DB::table('jadwal_sidang')
            ->join('sesi_ta', 'jadwal_sidang.sesi_id', '=', 'sesi_ta.sesi_id')
            ->join('ruangan_ta', 'jadwal_sidang.ruangan_id', '=', 'ruangan_ta.ruangan_id')
            ->where('tgl_sidang', '>', date('Y-m-d'));
        return $log;
    }
}
