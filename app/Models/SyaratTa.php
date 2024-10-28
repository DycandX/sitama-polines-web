<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SyaratTa extends Model
{
    use HasFactory;
    protected $table = "syarat_sidang";

    protected $primaryKey = "syarat_sidang_id";
    protected $fillable = [
        'ta_sidang_id',
        'ta_id',
        'dokumen_id',
        'dokumen_file_original',
        'dokumen_file',
        'verified'
    ];
    public $timestamps = false;

    public static function DaftarTa($id)
    {

        $log = DB::table('syarat_sidang')
            ->join('ta_sidang', 'syarat_sidang.ta_sidang_id', '=', 'ta_sidang.ta_sidang_id')
            ->join('dokumen_syarat_ta', 'syarat_sidang.dokumen_id', '=', 'dokumen_syarat_ta.dokumen_id')
            ->where('users.id', '=', $id);
        // left join ke tabel sidang
        return $log;
    }

    public static function dokumenSyaratTa($id)
    {
        $dokumenSyaratTa = DB::table('dokumen_syarat_ta')
            ->where("dokumen_syarat_ta.is_active", 1)
            ->leftJoin('syarat_sidang', function ($join) use ($id) {
                $join->on('syarat_sidang.dokumen_id', '=', 'dokumen_syarat_ta.dokumen_id')
                    ->where('syarat_sidang.ta_id', '=', function ($taId) use ($id) {
                        $taId->select('tas.ta_id')
                            ->from('tas')
                            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
                            ->join('users', 'users.email', '=', 'mahasiswa.email')
                            ->where('users.id', '=', $id)
                            ->limit(1);
                    });
            })
            ->select(
                'dokumen_syarat_ta.dokumen_id',
                'dokumen_syarat_ta.dokumen_syarat',
                'syarat_sidang.syarat_sidang_id',
                'syarat_sidang.ta_id',
                'syarat_sidang.dokumen_file_original',
                'syarat_sidang.dokumen_file',
                'syarat_sidang.verified',
            )
            ->get();
        // dd($dokumenSyaratTa);
        return $dokumenSyaratTa;
    }
}
