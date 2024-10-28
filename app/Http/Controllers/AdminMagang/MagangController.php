<?php


namespace App\Http\Controllers\AdminMagang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\magang;
use App\Models\Menu;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Support\Facades\DB;

class MagangController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read_magang')->only('index', 'show');
        $this->middleware('permission:create_magang')->only('create', 'store');
        $this->middleware('permission:update_magang')->only('edit', 'update');
        $this->middleware('permission:delete_magang')->only('destroy');
    }

    public function index(Request $request)
    {
        $tahun_akademik = $request->input('ta', '2023/2024');
        $data = Magang::data($tahun_akademik);
        // dd($data);
        return view('magang.index', compact('data'));
    }

    public function edit($magang_id)
    {
        $magang = Magang::magang($magang_id);
        $dosens = Dosen::all(); // Kirim parameter magang_id
        return view('magang.edit', compact('magang', 'dosens'));
    }

    public function editDosen(Request $request)
    {
        // Validasi input
        $request->validate([
            'dosen_nip' => 'required|string|max:255',
            'magang_id' => 'required|integer'
        ]);

        $magangId = $request->input('magang_id');
        $dosen= $request->input('dosen_nip'); 

        $tambah = DB::table('magangs')
                      ->where('magang_id', $magangId)
                      ->update(['dosen_nip' => $dosen]);
        return redirect()->route('magang.index')->with('success', 'Plotting sukses.');
    }

    public function syarat($magang_id)
    {
        $syarat = Magang::syarat($magang_id);
        return view('magang.syarat', compact('syarat'));
    }

    public function nilai($magang_id)
    {
        $nilai = Magang::nilai($magang_id);
        return view('magang.nilai', compact('nilai'));
    }

 

    public function verify(Request $request)
    {
        // Validasi input
        $request->validate([
            'data-id' => 'required|integer|exists:magang_syarats,magang_syarat_id'
        ]);

        // Ambil data-id dari request
        $id = $request->input('data-id');
        $verify = magang::find($id);
        $verify->status_valid = 1;
        $verify->save();

        return redirect()->route('magang.syarat')->with('success', 'Status berhasil diubah.');
    }


   

    public function show(magang $data)
    {
        //
    }

}
