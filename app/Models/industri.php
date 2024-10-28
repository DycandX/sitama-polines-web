<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class industri extends Model
{
    protected $primaryKey = 'industri_id'; // assuming mhs_nim is the primary key

    public function industriMhs()
    {
        return $this->hasMany(industri::class, 'industri_id');
    }
}