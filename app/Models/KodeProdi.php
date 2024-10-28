<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeProdi extends Model
{
    use HasFactory;
    protected $table = 'kode_prodi';
    protected $primaryKey = 'prodi_ID';
    protected $fillable = ['prodi_ID', 'program_studi'];
    public $timestamps = false; 
}

