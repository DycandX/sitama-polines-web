<?php

namespace App\Http\Controllers\MhsMagang;

use App\Http\Controllers\Controller;
use App\Models\industri;
use App\Models\Magang;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\pendaftaran_magang;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {

        $industriid = industri::where("industri_id", $request->industri_id)->first();
        $dataindustri = industri::all();
        $kota = Magang::kota();

        $mhs = Magang::BimbinganByMagang();

        if (isset($mhs->magang_id)) {
            $datamhs = Magang::datamhs22($mhs);


            session(['magang_id' => $mhs->magang_id]);
            $datadosen = Magang::datadosen();
            // dd($datadosen);
            return view('pendaftaran.data', compact('datamhs','datadosen'));
        }
        else {
            return view('pendaftaran.daftar', compact('dataindustri', 'industriid','kota'));
        }
    }

    public function store(Request $request)
    {
        // Inisialisasi variabel untuk menyimpan industri_id
        $industri_id = $request->nama_industri;

        // Jika industri baru diinputkan
        if ($request->nama_industri == 'new' && $request->new_nama_industri) {
            // Insert industri baru dan dapatkan ID-nya
            $industri_id = DB::table('industris')->insertGetId(
                [
                    'nama_industri' => $request->new_nama_industri,
                    'alamat' => $request->alamat,
                    'kota' => $request->kota
                ]
            );
        }

        // Dapatkan NIM mahasiswa yang sedang magang
        $nim = Magang::Mhsmagang()->mhs_nim;

        // Dapatkan tahun ajaran yang sedang aktif
        $ta = DB::table('master_ta')
                ->select('ta')
                ->where('status', 1)
                ->first()
                ->ta;

        // Insert data magang dan dapatkan ID-nya
        $magang_id = DB::table('magangs')->insertGetId(
            [
                'mhs_nim' => $nim,
                'ta' =>  $ta,
            ]
        );

        // Insert data magang_industris dengan industri_id yang sesuai
        DB::table('magang_industris')->insert(
            [
                'magang_id' => $magang_id,
                'industri_id' => $industri_id,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_selesai' => $request->tgl_selesai,
            ]
        );

        return redirect()->route('daftar-magang.index')->with('success', 'Data berhasil disimpan.');
    }

    public function show($magang_id)
    {
        $menus = Menu::all();
        $data = Magang::datamhs($magang_id);
        return view('pendaftaran.data', compact('menus', 'data'));
    }

    public function create()
    {
        
        $dataindustri = industri::all();
        $kota = Magang::kota();
        $magangid = Magang::magangId();

        $mhs = Magang::BimbinganByMagang();
        return view('pendaftaran.tambah', compact('dataindustri','kota', 'mhs', 'magangid'));

        
    }

    public function storeTambah(Request $request)
    {
        // Inisialisasi variabel untuk menyimpan industri_id
        $industri_id = $request->nama_industri;

        // Jika industri baru diinputkan
        if ($request->nama_industri == 'new' && $request->new_nama_industri) {
            // Insert industri baru dan dapatkan ID-nya
            $industri_id = DB::table('industris')->insertGetId(
                [
                    'nama_industri' => $request->new_nama_industri,
                    'alamat' => $request->alamat,
                    'kota' => $request->kota
                ]
            );
        }

        $magang_id = session('magang_id');

        // Insert data magang_industris dengan industri_id yang sesuai
        DB::table('magang_industris')->insert(
            [
                'magang_id' => $magang_id,
                'industri_id' => $industri_id,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_selesai' => $request->tgl_selesai,
            ]
        );

        return redirect()->route('daftar-magang.index')->with('success', 'Data berhasil disimpan.');
    }
}