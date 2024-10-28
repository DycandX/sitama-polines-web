<?php

namespace App\Http\Controllers\AdminMagang;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Menu;

class DasboardController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return view('dasboard.dasbor', compact('menus'));
    }

    // Fungsi lainnya bisa ditambahkan sesuai kebutuhan
}

?>
