<?php

namespace App\Http\Controllers\API\MahasiswaTa;

use App\Http\Controllers\Controller;
use App\Models\Bimbingan;
use App\Models\Ta;
use App\Models\TaSidang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

// Mahasiswa
class SidangTaController extends Controller
{
    public function index()
    {
        $id = Auth::user()->id;
        $mahasiswa = Bimbingan::Mahasiswa($id);

        if (!isset($mahasiswa)) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan'
            ], 404);
        }

        Carbon::setLocale('id');
        $tanggal_sidang = Carbon::parse($mahasiswa->tgl_sidang)->translatedFormat('l, j F Y');
        $hari_sidang = Carbon::parse($mahasiswa->tgl_sidang)->translatedFormat('l');

        $dataTa = Ta::dataTa($id);
        $dosenNip = Bimbingan::Mahasiswa($id)->dosen_nip;
        $dosenNama = Bimbingan::Mahasiswa($id)->dosen_nama;
        $bimbinganId = Bimbingan::Mahasiswa($id)->bimbingan_id;

        if (!isset($dataTa)) {
            return response()->json([
                'success' => false,
                'message' => 'Isikan Judul Proposal Terlebih Dahulu'
            ], 400);
        } elseif (!isset($dosenNama, $dosenNip, $bimbinganId)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda Belum Mendapatkan Dosen Pembimbing'
            ], 400);
        } else {
            $taSidang = TaSidang::where('ta_id', $dataTa->ta_id)->first();
            return response()->json([
                'success' => true,
                'data' => [
                    'mahasiswa' => $mahasiswa,
                    'taSidang' => $taSidang,
                    'tanggal_sidang' => $tanggal_sidang,
                    'hari_sidang' => $hari_sidang
                ]
            ]);
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
            return response()->json([
                'success' => false,
                'message' => 'File gagal diupload. Periksa kembali data anda.',
                'errors' => $validator->errors()
            ], 400);
        }

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

            return response()->json([
                'success' => true,
                'message' => 'File revisi berhasil diupload'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat masalah di server: ' . $th->getMessage()
            ], 500);
        }
    }

}
