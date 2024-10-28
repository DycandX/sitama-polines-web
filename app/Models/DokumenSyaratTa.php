<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenSyaratTa extends Model
{
    use HasFactory;

    protected $table = 'dokumen_syarat_ta';
    protected $primaryKey = 'dokumen_id';
    protected $guarded = ['dokumen_id', 'dokumen_syarat'];
}
