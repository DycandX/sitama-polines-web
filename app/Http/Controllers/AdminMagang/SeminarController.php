<?php


namespace App\Http\Controllers\AdminMagang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seminar;
use App\Models\Mahasiswa;
use App\Models\Menu;
use App\Models\Ruangan;
use App\Models\magang;
// use App\Models\DB;
use Illuminate\Support\Facades\DB;



use PhpParser\Node\Stmt\Do_;

class SeminarController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read_seminar')->only('index', 'show');
        $this->middleware('permission:create_seminar')->only('create', 'store');
        $this->middleware('permission:update_seminar')->only('edit', 'update');
        $this->middleware('permission:delete_seminar')->only('destroy');
    }

    public function index()
    {
        $data = Seminar::data();
        //
        return view('seminar.index', compact('data'));
    }

    public function create()
    {
        $menus = Menu::all();
        $ruangan_ta = Ruangan::all();
        $mahasiswas = Mahasiswa::all();
        $validasi = Seminar::daftarmhs();
        return view('seminar.create', compact('ruangan_ta', 'mahasiswas', 'validasi'));
    }

    public function store(Request $request)
    {
        // Log data request
    
        // Validasi data yang diterima dari form
        $validatedData = $request->validate([
            'magang_id' => 'required',
            'tgl_seminar' => 'required|date',
            'ruangan_id' => 'required',
            'waktu' => 'required|date_format:H:i',
        ]);
    
        // Update atau insert data ke tabel seminar_magangs
        DB::table('seminar_magangs')->updateOrInsert(
            ['magang_id' => $request->magang_id], 
            [
                'ruangan_id' => $request->ruangan_id,
                'tgl_seminar' => $request->tgl_seminar,
                'waktu' => $request->waktu,
                'status_seminar' => '0'
            ]
            );
            return redirect()->route('seminar.index')->with('success', 'Data berhasil disimpan.');
        }


        public function show($magang_id)
        {
            $menus = Menu::all();
            $magangs = magang::nilai($magang_id);
            $ruangan_ta = Ruangan::all();
            $mahasiswas = Mahasiswa::all();
            $validasi = Seminar::daftarmhs();
            return view('seminar.edit', compact('menus','magangs', 'ruangan_ta', 'magangs', 'mahasiswas', 'validasi'));
        }
    
        public function ubah(Request $request)
        {
            $request->validate([
                'magang_id' => 'required',
                'tgl_seminar' => 'required|date',
                'ruangan_id' => 'required',
                'waktu' => 'required|date_format:H:i',
            ]);
    
            $magangId = $request->input('magang_id');
            $ruangan= $request->input('ruangan_id'); 
            $tanggal= $request->input('tgl_seminar'); 
            $waktu= $request->input('waktu'); 
    
            $tambah = DB::table('seminar_magangs')
                          ->where('magang_id', $magangId)
                          ->update([
                            'ruangan_id' => $ruangan,
                            'tgl_seminar' => $tanggal,
                            'waktu' => $waktu,
                            ]);
            return redirect()->route('seminar.index')->with('success', 'Jadwal diubah.');
    
        }

    public function destroy($magang_id)
    {
        
        DB::table('seminar_magangs')->where('magang_id', $magang_id)->delete();

       
        return redirect()->route('seminar.index')->with('success', 'Data berhasil dihapus.');
    }

    public function valid(Request $request)
    {
        // Validasi input
        $request->validate([
            'data-id' => 'required|integer|exists:seminar_magangs,seminar_id'
        ]);

        // Ambil data-id dari request
        $id = $request->input('data-id');
        $verify = Seminar::find($id);
        $verify->status_seminar = 1;
        $verify->save();

        return redirect()->route('seminar.index')->with('success', 'Status validasi berhasil diubah.');
    }

}
