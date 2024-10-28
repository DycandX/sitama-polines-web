<?php

namespace App\Http\Controllers\MhsMagang;

use App\Http\Controllers\Controller;

use App\Models\Magang;
use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use App\Models\bimbingan_magang;

class JadwalBimbinganController extends Controller
{
    public function index(Request $request)
    {
        $email = Auth::user()->email;
        $magang = Magang::BimbinganMHS();
        // return view('jadwalbimbingan.jadwalbimbingan', ['bimbingan_magangs' => $data]);
        $magangid = Magang::magangId();
        if (!isset($magangid)) {
            toastr()->warning('Anda Belum Daftar');
            return redirect()->route('daftar-magang.index');
        } else {
            return view('bimbinganmhs.index', compact('magang'));
        }
    }
    public function create()
    {
        return view('bimbinganmhs.create');
    }
    //method untuk tambah data buku
    public function store(Request $request)
    {
        // Validasi data dari form
        $this->validate($request, [
            'tgl_kegiatan' => 'required',
            'kegiatan' => 'required',
        ]);

        // Mengambil magang_id dari session
        $magang_id = session('magang_id');

        // Simpan data ke database
        DB::table('bimbingan_magangs')->insert(
            [
                'magang_id' => $magang_id,
                'tgl_kegiatan' => $request->tgl_kegiatan,
                'kegiatan' => $request->kegiatan,
                'status' => '0'
            ]
        );

        // Redirect ke halaman lain dengan pesan sukses
        return redirect()->route('jadwal-bimbingan.index')->with('success', 'Data berhasil disimpan.');
    }

    public function edit($bimbingan_magang_id)
    {

        $bimbingan = bimbingan_magang::find($bimbingan_magang_id);


        return view('bimbinganmhs.edit', compact('bimbingan'));
    }

    public function update(Request $request, $bimbingan_magang_id)
    {
        // Validasi data dari form
        $request->validate([
            'tgl_kegiatan' => 'required',
            'kegiatan' => 'required',

        ]);

        $bimbingan = bimbingan_magang::find($bimbingan_magang_id);

        $bimbingan->tgl_kegiatan = $request->tgl_kegiatan;
        $bimbingan->kegiatan = $request->kegiatan;
        $bimbingan->update();

        // Redirect ke halaman lain dengan pesan sukses
        return redirect()->route('jadwal-bimbingan.index')->with('success', 'Data berhasil disimpan.');
    }
    public function destroy($id)
    {
        $data = bimbingan_magang::find($id);
        $data->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}
