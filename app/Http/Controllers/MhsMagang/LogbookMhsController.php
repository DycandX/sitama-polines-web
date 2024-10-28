<?php

namespace App\Http\Controllers\MhsMagang;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\logbook_magang;
use App\Models\Magang;

class LogbookMhsController extends Controller
{
    public function index()
    {
        // $data = logbook_magang::all();
        $data = logbook_magang::LogbookByMagang();
        $magangid = Magang::magangId();
        if (!isset($magangid)) {
            toastr()->warning('Anda Belum Daftar');
            return redirect()->route('daftar-magang.index');
        } else {
            return view('logbook.index', ['name' => 'Logbook', 'logbook_magangs' => $data]);
        }
    }
    public function create()
    {
        return view('logbook.create');
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
        $data = new logbook_magang();
        $data->tgl_kegiatan = $request->tgl_kegiatan;
        $data->kegiatan = $request->kegiatan;
        $data->magang_id = $magang_id; // Menggunakan magang_id dari session
        $data->status_kehadiran = '0';
        $data->save();

        // Redirect ke halaman lain dengan pesan sukses
        return redirect()->route('logbook-magang.index')->with('success', 'Data berhasil disimpan.');
    }

    //mengambil show
    public function show($logbook_id)
    {
        $logbook_magang = logbook_magang::find($logbook_id);
        return view('logbook.show', compact('logbook_magang'));
    }
    //method untuk hapus data
    public function destroy($id)
    {
        $data = logbook_magang::find($id);
        $data->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }

    //method untuk form edit
    public function edit($logbook_id)
    {
        // Mengambil data logbook_magang berdasarkan logbook_id
        $logbook_magang = logbook_magang::find($logbook_id);

        // Mengirimkan data logbook_magang ke view untuk ditampilkan di dalam form
        return view('logbook.edit', compact('logbook_magang'));
    }

    //method untuk update
    public function update($logbook_id, Request $request)
    {
        // Validasi data dari form
        $request->validate([
            'tgl_kegiatan' => 'required',
            'kegiatan' => 'required',

        ]);

        // Mengambil data logbook_magang berdasarkan logbook_id
        $logbook_magang = logbook_magang::find($logbook_id);

        // Mengupdate data logbook_magang
        $logbook_magang->tgl_kegiatan = $request->tgl_kegiatan;
        $logbook_magang->kegiatan = $request->kegiatan;
        $logbook_magang->save();

        // Redirect ke halaman lain dengan pesan sukses
        return redirect()->route('logbook-magang.index')->with('success', 'Data berhasil disimpan.');
    }
}
