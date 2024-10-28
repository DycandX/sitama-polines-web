<?php

namespace App\Http\Controllers\MahasiswaTa;

use App\Http\Controllers\Controller;
use App\Models\Bimbingan;
use App\Models\Ta;
use App\Models\TaSidang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

// Mahasiswa
class SidangTaController extends Controller
{
    public function index()
    {
        $id = Auth::user()->id;
        $mahasiswa = Bimbingan::Mahasiswa($id);
        Carbon::setLocale('id');
        $tanggal_sidang = Carbon::parse($mahasiswa->tgl_sidang)->translatedFormat('l, j F Y');
        $hari_sidang = Carbon::parse($mahasiswa->tgl_sidang)->translatedFormat('l');

        $dataTa = Ta::dataTa($id);
        $dosenNip = Bimbingan::Mahasiswa($id)->dosen_nip;
        $dosenNama = Bimbingan::Mahasiswa($id)->dosen_nama;
        $bimbinganId = Bimbingan::Mahasiswa($id)->bimbingan_id;

        if (!isset($dataTa)) {
            toastr()->error('Isikan Judul Proposal Terlebih Dahulu');
            return redirect('/dashboard-mahasiswa');
        } elseif (!isset($dosenNama, $dosenNip, $bimbinganId)) {
            toastr()->error('Anda Belum Mendapatkan Dosen Pembimbing');
            return redirect('/dashboard-mahasiswa');
        } else {
            $taSidang = TaSidang::where('ta_id', $dataTa->ta_id)->first();
            return view('sidang-ta.index', compact('mahasiswa', 'taSidang', 'tanggal_sidang', 'hari_sidang'));
        }
    }

    public function suratTugas()
    {
    }

    public function store(Request $request)
    {
        $id = Auth::user()->id;
        $taId = Bimbingan::Mahasiswa($id)->ta_id;

        $validator = Validator::make($request->all(), [
            'draft_revisi' => 'required|mimetypes:application/pdf|max:2048'
        ]);

        if ($validator->fails()) {
            toastr()->error('File Gagal diupload </br> Periksa kembali data anda');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        };

        $taSidang = TaSidang::where('ta_id', $taId)->first();

        if ($taSidang) {
            $fileLama = public_path('storage/draft_revisi/' . $taSidang->revisi_file);
            if (file_exists($fileLama) && !empty($taSidang->revisi_file)) {
                unlink($fileLama);
            }
        }

        try {
            $draft_revisi = $request->file('draft_revisi');
            $revisi_file = date('Ymdhis') . '.' . $draft_revisi->getClientOriginalExtension();
            $draft_revisi->storeAs('public/draft_revisi', $revisi_file);

            TaSidang::updateOrCreate(
                ['ta_id' => $taId],
                [
                    'revisi_file_original' => $draft_revisi->getClientOriginalName(),
                    'revisi_file' => $revisi_file,
                ]
            );
            toastr()->success('File Revisi berhasil terupload');
            return redirect()->route('sidang-tugas-akhir.index');
        } catch (\Throwable $th) {
            toastr()->warning('Terdapat masalah diserver' . $th->getMessage());
            return redirect()->route('sidang-tugas-akhir.index');
        }
    }

    public function upload_lembar_pengesahan() {
        return view('sidang-ta.upload-lembar-pengesahan');
    }

    public function upload_lembar(Request $request) {
        $validator = Validator::make($request->all(), [
            'file_name' => 'required|mimetypes:application/pdf|max:2048'
        ]);

        $id = Auth::user()->id;
        $mahasiswa = Bimbingan::Mahasiswa($id);
        $mhs = $mahasiswa->mhs_nim;
        
        $file = $request->file('file');
        $fileName = date('Ymdhis') . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/lembar_pengesahan', $fileName);
        $file_name_original = $file->getClientOriginalName();

        DB::table('lembar_pengesahan')->insert([
            'file_name' => $fileName,
            'file_name_original' => $file_name_original,
            'mhs_nim' => $mhs,
        ]);

        return redirect('sidang-tugas-akhir');
    }
}
