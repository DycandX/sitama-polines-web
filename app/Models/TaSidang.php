<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TaSidang extends Model
{
    use HasFactory;

    protected $table = "ta_sidang";
    protected $primaryKey = "ta_sidang_id";
    protected $fillable = ['ta_id', 'jadwal_id', 'judul_final', 'nilai_akhir', 'status_lulus', 'revisi_file', 'revisi_file_original'];
    public $timestamps = false;
}
