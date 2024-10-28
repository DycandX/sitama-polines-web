<?php


namespace App\Http\Controllers\DosenMagang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\mahasiswa;
use App\Models\Magang;
use App\Models\laporan_magang;
use App\Models\Bimbingan_magang;
use Illuminate\Support\Facades\DB;



class DataBimbinganController extends Controller
{
    public function index(Request $request)
    {
        // $menus = Menu::all();
        // $data= magang_mhs::all();
        $tahun_akademik = $request->input('ta', '2023/2024');

        // Query data magang berdasarkan tahun akademik yang dipilih
        // $data = Magang::where('ta', $tahun_akademik)->magang_mhs();
        $data= Magang::magang_mhs($tahun_akademik);
        $logbook= Magang::logbook_mhs();
        // return view('bimbingan.databimbingan', ['mahasiswas'=>$data]);
        return view('bimbingan.databimbingan', compact('data', 'logbook'));
        
        // return view('bimbingan.databimbingan', compact('menus'));
    }

    public function contoh()
    {
        return view('bimbingan.coba'); // Menampilkan view 'contoh.blade.php' di folder 'bimbingan'
    }
    
    public function logbook($magang_id)
    {   
        $validasi = Magang::validasimagangg($magang_id);
        $logbook = Magang::logbook_mhs($magang_id); // Kirim parameter magang_id
        return view('bimbingan.logbook', compact('logbook', 'validasi'));
    }

    public function laporan($magang_id)
    {
        $validasi = Magang::validasimagangg($magang_id);
        $laporan = laporan_magang::laporan_mhs($magang_id);
        return view('bimbingan.laporan', compact('laporan', 'validasi'));
    }

    public function bimbingan($magang_id)
    {
        $validasi = Magang::validasimagangg($magang_id);
        $bimbingan= Bimbingan_magang::bimbingan_mhs($magang_id);
        return view('bimbingan.bimbingan', compact('bimbingan','validasi'));
    }

    public function verify(Request $request)
    {
        // Validasi input
        $request->validate([
            'data-id' => 'required|integer|exists:bimbingan_magangs,bimbingan_magang_id'
        ]);

        // Ambil data-id dari request
        $id = $request->input('data-id');
        $bimbinganMagang = Bimbingan_magang::find($id);
        $bimbinganMagang->status = 1;
        $bimbinganMagang->save();

        return redirect()->route('bimbingan-dosen-magang.bimbingan', ['magang_id' => $bimbinganMagang->magang_id])->with('success', 'Status berhasil diubah.');
    }

    public function validasi($magang_id)
    {   
        $validasi = Magang::validasimagangg($magang_id);
        return view('bimbingan.validasi', compact('validasi'));
    }

    public function valid(Request $request) 
    {
       
        
        $validatedData = $request->validate([
            'magang_id' => 'required',
           
        ]);
    
        DB::table('validasi_magangs')->updateOrInsert(
            ['magang_id' => $request->magang_id], 
            [
                'laporan_valid' => '1',
                'proposal_valid' => '1',
                'logbook_valid' => '1',
            ]
        );
    
        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('bimbingan-dosen-magang.index')->with('success', 'Data berhasil disimpan.');
    }
    
    // Fungsi lainnya bisa ditambahkan sesuai kebutuhan
}
?>
