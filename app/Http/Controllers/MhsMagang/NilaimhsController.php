<?php

namespace App\Http\Controllers\MhsMagang;

use App\Http\Controllers\Controller;

use App\Models\Magang;
use App\Models\nilai;
use App\Models\Tipe_laporan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Penilian;


use Illuminate\Http\Response;




class NilaimhsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Penilian::nilaimhsss();
        $komponen = Penilian::komponenmhs();
        $magangid = Magang::magangId();
        if (!isset($magangid)) {
            toastr()->warning('Anda Belum Daftar');
            return redirect()->route('daftar-magang.index');
        } else {
            return view('nilai.tampilanmhs', compact('data', 'komponen'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     $data = nilai::find($id);
    //     return view('nilai.show')->with('nilai', $data);
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
