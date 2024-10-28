<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dosen extends Model
{
    use HasFactory;
    protected $table = 'dosen';
    protected $primaryKey = 'dosen_nip';
    public $guarded = ['dosen_nip'];

    public static function dosenNip()
    {
        $id = Auth::user()->id;

        $nip = DB::table('dosen')
            ->join('users', 'users.email', '=', 'dosen.email')
            ->select('dosen.dosen_nip')
            ->where('users.id', $id)
            ->first();

        return $nip;
    }
}
