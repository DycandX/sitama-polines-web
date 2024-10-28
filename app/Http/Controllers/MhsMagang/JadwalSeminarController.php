<?php

namespace App\Http\Controllers\MhsMagang;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\JadwalSeminar;
use App\Models\Magang;
use App\Models\Seminar;
use App\Models\Menu;

class JadwalSeminarController extends Controller
{
    public function index()
    {
        $seminar = Seminar::seminarmhs();
        $magangid = Magang::magangId();
        if (!isset($magangid)) {
            toastr()->warning('Anda Belum Daftar');
            return redirect()->route('daftar-magang.index');
        } else {
            return view('seminar.tampilanmhs',compact('seminar'));
        }
    }
}
