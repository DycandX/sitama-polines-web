<?php

namespace App\Http\Controllers\MhsMagang;

use App\Http\Controllers\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Laporan_magang;
use App\Models\Magang;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;


class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Laporan_magang::TambahDokumen();
        $magangid = Magang::magangId();
        if (!isset($magangid)) {
            toastr()->warning('Anda Belum Daftar');
            return redirect()->route('daftar-magang.index');
        } else {
            return view('laporanmagang.index', compact('data'));
        }
    }
    public function create()
    {
        return view('laporanmagang.create');
    }
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'magang_judul' => 'required',
            'file_magang' => 'required|mimetypes:application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf',
        ]);

        $file_magang = $request->file('file_magang');
        $nama_dokumen = 'FT' . date('Ymdhis') . '.' . $request->file('file_magang')->getClientOriginalExtension();
        $file_magang->move('storage', $nama_dokumen);

        $magang_id = session('magang_id');



        DB::table('laporan_magangs')->insert(
            [
                'magang_id' => $magang_id,
                'magang_judul' => $request->magang_judul,
                'file_magang_original' => $file_magang->getClientOriginalName(),
                'file_magang' => $nama_dokumen,
                'tipe' => $request->jenis_dokumen,
            ],
        );

        return redirect('laporanmagang')->with('success', 'Data berhasil disimpan.');
    }


    public function edit(string $laporan_id): View
    {
        $data = Laporan_magang::find($laporan_id);
        return view('laporanmagang.edit')->with('laporanmagang', $data);
    }

    public function update(Request $request, string $laporan_id): RedirectResponse
    {
        $this->validate($request, [
            'magang_judul' => 'required',
            'file_magang' => 'nullable|mimetypes:application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ]);

        $dataToUpdate = [
            'magang_judul' => $request->magang_judul,
            'tipe' => $request->jenis_dokumen,
        ];

        // Handle file upload
        if ($request->hasFile('file_magang')) {
            $file_magang = $request->file('file_magang');
            $nama_dokumen = 'FT' . date('Ymdhis') . '.' . $file_magang->getClientOriginalExtension();
            $file_magang->move('storage', $nama_dokumen);
            $dataToUpdate['file_magang'] = $nama_dokumen;
        }

        DB::table('laporan_magangs')
            ->where('laporan_id', $laporan_id)
            ->update($dataToUpdate);

        return redirect('laporanmagang')->with('flash_message', 'Laporan magang updated!');
    }

    public function destroy(string $laporan_id): RedirectResponse
    {
        Laporan_magang::destroy($laporan_id);
        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}
