<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SyaratSidang extends Model
{
    use HasFactory;
    protected $table = 'syarat_sidang';
    protected $primaryKey = 'syarat_sidang_id';
    protected $fillable = [
        'ta_sidang_id',
        'ta_id',
        'dokumen_id',
        'dokumen_nama',
        'verified'
    ];
    public $timestamps = false;

    public static function syaratMahasiswa($ta_id)
    {
        $syarat = DB::table('dokumen_syarat_ta')
            ->leftJoin('syarat_sidang', function ($join) use ($ta_id) {
                $join->on('syarat_sidang.dokumen_id', '=', 'dokumen_syarat_ta.dokumen_id')
                    ->where('syarat_sidang.ta_id', '=', $ta_id);
            })
            ->where('dokumen_syarat_ta.is_active', 1)
            ->select(
                'dokumen_syarat_ta.dokumen_id',
                'dokumen_syarat_ta.dokumen_syarat',
                'syarat_sidang.syarat_sidang_id',
                'syarat_sidang.ta_id',
                'syarat_sidang.dokumen_file',
                'syarat_sidang.dokumen_file_original',
                'syarat_sidang.verified',
            )
            ->get();
        // dd($syarat);
        return $syarat;
    }
}
