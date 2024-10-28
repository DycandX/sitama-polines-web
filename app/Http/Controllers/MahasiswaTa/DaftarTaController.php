<?php

namespace App\Http\Controllers\MahasiswaTa;

use App\Http\Controllers\Controller;
use App\Models\Bimbingan;
use App\Models\BimbinganLog;
use App\Models\DokumenSyaratTa;
use App\Models\JadwalSidang;
use App\Models\SyaratTa;
use App\Models\Ta;
use App\Models\TaSidang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

// Mahasiswa
class DaftarTaController extends Controller
{

    public function index()
    {
        $id = Auth::user()->id;
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
            $mahasiswa = Bimbingan::Mahasiswa($id);
            $dokumenSyaratTa = SyaratTa::dokumenSyaratTa($id);

            Carbon::setLocale('id');
            $jadwal = JadwalSidang::DaftarTa()->get()->sortBy('jadwal_id');

            foreach ($jadwal as $jd) {
                $temp_tgl = $jd->tgl_sidang;
                $jd->tgl_sidang = Carbon::parse($temp_tgl)->translatedFormat('l, j F Y');
                $jd->hari_sidang = Carbon::parse($temp_tgl)->translatedFormat('l');
            }

            $jadwalAda = DB::table('ta_sidang')->pluck('jadwal_id')->toArray();

            $BimbinganLog = BimbinganLog::BimbinganLog($id);
            $jumlahBimbingan1 = $BimbinganLog->where('urutan', 1)->where('bimb_status', 1)->count();
            $jumlahBimbingan2 = $BimbinganLog->where('urutan', 2)->where('bimb_status', 1)->count();

            $masterJumlah = DB::table('bimbingan_counts')
                ->value('bimbingan_counts.total_bimbingan');

            $memenuhiBimbingan = $jumlahBimbingan1 >= $masterJumlah && $jumlahBimbingan2 >= $masterJumlah;

            $memenuhiSyarat = $dokumenSyaratTa->every(function ($item) {
                return $item->dokumen_file != null && $item->verified == 1;
            });

            $taSidang = TaSidang::where('ta_id', $dataTa->ta_id)->first();
            $verifikasiPembimbing = collect($mahasiswa->dosen)->every(function ($item) {
                return $item['verified'] == 1;
            });

            //GET DATA PARTNER
            $partner = DB::selectOne("SELECT
                        TAM1.mhs_nim,
                        tas.ta_id
                    FROM
                        `tas_mahasiswa` TAM
                    JOIN tas_mahasiswa TAM1 ON
                        TAM.ta_id = TAM1.ta_id AND TAM.mhs_nim <> TAM1.mhs_nim
                    JOIN tas ON 
                        TAM1.mhs_nim = tas.mhs_nim
                    WHERE
                        TAM.mhs_nim = '" . $mahasiswa->mhs_nim . "';");

            $partner_valid = isset($partner->mhs_nim) ? $this->is_mhs_eligible_sidang($partner->mhs_nim) : NULL;

            

            return view('daftar-ta.index', compact('BimbinganLog', 'jadwal', 'jadwalAda', 'dokumenSyaratTa', 'memenuhiSyarat', 'memenuhiBimbingan', 'taSidang', 'masterJumlah', 'mahasiswa', 'verifikasiPembimbing', 'partner', 'partner_valid'));
        }
    }

    public function is_mhs_eligible_sidang($mhs_nim)
    {
        $setting_bimbingan = DB::selectOne("SELECT * FROM bimbingan_counts");

        $pembimbingan = DB::select("SELECT * FROM v_dosen_aktifitas_bimbingan WHERE mhs_nim = '" . $mhs_nim . "';");

        $is_valid = 1;
        foreach ($pembimbingan as $row) {
            if ($row->jml_aktivitas_pembimbingan_valid < $setting_bimbingan->total_bimbingan) {
                $is_valid = 0;
            }
            if ($row->verified != '1') {
                $is_valid = 0;
            }
        }

        $dokumen_lengkap_valid = DB::select("SELECT 
            DST.dokumen_id, 
            DST.dokumen_syarat,
            IF(SS.syarat_sidang_id IS NOT NULL,1,0) sudah_upload,
            SS.verified
        FROM `dokumen_syarat_ta` DST 
            LEFT JOIN syarat_sidang SS 
                ON DST.dokumen_id = SS.dokumen_id 
                AND SS.ta_id = (SELECT ta_id FROM tas WHERE mhs_nim = " . $mhs_nim . ") WHERE DST.is_active = 1;");

        foreach ($dokumen_lengkap_valid as $row) {
            if ($row->sudah_upload == 0) {
                $is_valid = 0;
            }
            if ($row->verified != 1) {
                $is_valid = 0;
            }
        }

        return $is_valid;
    }

    public function uploadSingle(Request $request)
    {
        $id = Auth::user()->id;
        $taId = Bimbingan::Mahasiswa($id)->ta_id;

        $validator = Validator::make($request->all(), [
            'draft_syarat' => 'required|mimetypes:application/pdf|max:2048000',
        ]);


        if ($validator->fails()) {
            toastr()->error('Gagal mengupload </br> Periksa kembali data anda');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        };
        $dokumen = DokumenSyaratTa::where('dokumen_id', $request->post('dokumen_id'))->first();

        $syaratTa = SyaratTa::where('dokumen_id', $request->post('dokumen_id'))
            ->where('ta_id', $taId)
            ->first();

        if ($syaratTa) {
            $fileLama = public_path('storage/syarat_ta/' . $syaratTa->dokumen_file);
            if (file_exists($fileLama) && !empty($syaratTa->dokumen_file)) {
                unlink($fileLama);
            }
        }

        try {
            $draftSyarat = $request->file('draft_syarat');
            $namaFile = date('Ymdhis') . '.' . $draftSyarat->getClientOriginalExtension();
            $draftSyarat->storeAs('public/syarat_ta', $namaFile);

            SyaratTa::updateOrCreate(
                ['dokumen_id' => $request->post('dokumen_id'), 'ta_id' => $taId],
                [
                    'dokumen_file_original' => $draftSyarat->getClientOriginalName(),
                    'dokumen_file' => $namaFile,
                    'verified' => 0
                ]
            );


            toastr()->success($dokumen->dokumen_syarat . ' berhasil terupload');
            return redirect()->route('daftar-tugas-akhir.index');
        } catch (\Throwable $th) {
            toastr()->warning('Terdapat masalah diserver' . $th->getMessage());
            return redirect()->route('daftar-tugas-akhir.index');
        }
    }

    public function upload($id)
    {
        $idUser = Auth::user()->id;
        $dokumenSyaratTa = SyaratTa::dokumenSyaratTa($idUser)->where('dokumen_id', $id)->first();
        if ($dokumenSyaratTa->verified == 1) {
            toastr()->warning('Syarat Sudah Diverifikasi');
            return redirect()->route('daftar-tugas-akhir.index');
        } else {
            return view('daftar-ta.upload', compact('dokumenSyaratTa'));
        }
    }
    public function show($id)
    {
        $idUser = Auth::user()->id;
        $dokumenSyaratTa = SyaratTa::dokumenSyaratTa($idUser)->where('dokumen_id', $id)->first();

        return view('daftar-ta.show', compact('dokumenSyaratTa'));
    }

    public function store(Request $request)
    {
        $id = Auth::user()->id;
        $taId = Bimbingan::Mahasiswa($id)->ta_id;

        $validator = Validator::make($request->all(), [
            'draft_syarat' => 'required|mimetypes:application/pdf|max:2048',
        ]);


        if ($validator->fails()) {
            toastr()->error('Gagal mengupload </br> Periksa kembali data anda');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        };

        $syaratTa = SyaratTa::where('dokumen_id', $request->dokumenId)
            ->where('ta_id', $taId)
            ->first();

        if ($syaratTa) {
            $fileLama = public_path('storage/syarat_ta/' . $syaratTa->dokumen_file);
            if (file_exists($fileLama) && !empty($syaratTa->dokumen_file)) {
                unlink($fileLama);
            }
        }

        try {
            $draftSyarat = $request->file('draft_syarat');
            $namaFile = date('Ymdhis') . '.' . $draftSyarat->getClientOriginalExtension();
            $draftSyarat->storeAs('public/syarat_ta', $namaFile);

            SyaratTa::updateOrCreate(
                ['dokumen_id' => $request->dokumenId, 'ta_id' => $taId],
                [
                    'dokumen_file_original' => $draftSyarat->getClientOriginalName(),
                    'dokumen_file' => $namaFile,
                    'verified' => 0
                ]
            );


            toastr()->success('Syarat berhasil terupload');
            return redirect()->route('daftar-tugas-akhir.index');
        } catch (\Throwable $th) {
            toastr()->warning('Terdapat masalah diserver' . $th->getMessage());
            return redirect()->route('daftar-tugas-akhir.index');
        }
    }

    public function daftar(Request $request)
    {
        $id = Auth::user()->id;
        $taId = Bimbingan::Mahasiswa($id)->ta_id;

        $validator = Validator::make($request->all(), [
            'judulFinal' => 'Required',
            'jadwal' => 'Required'
        ]);

        if ($validator->fails()) {
            toastr()->error('Gagal mengupload </br> Periksa kembali data anda');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        };

        try {

            TaSidang::updateOrCreate(
                ['ta_id' => $taId],
                [
                    'jadwal_id' => $request->jadwal,
                    'judul_final' => $request->judulFinal,
                    'status_lulus' => 0
                ]
            );
            $partner = DB::selectOne("SELECT
                        TAM1.mhs_nim,
                        tas.ta_id
                    FROM
                        `tas_mahasiswa` TAM
                    JOIN tas_mahasiswa TAM1 ON
                        TAM.ta_id = TAM1.ta_id AND TAM.mhs_nim <> TAM1.mhs_nim
                    JOIN tas ON 
                        TAM1.mhs_nim = tas.mhs_nim
                    WHERE
                        TAM.mhs_nim = '" . Bimbingan::Mahasiswa($id)->mhs_nim . "';");

            if ($partner) {
                TaSidang::updateOrCreate(
                    ['ta_id' => $partner->ta_id],
                    [
                        'jadwal_id' => $request->jadwal,
                        'judul_final' => $request->judulFinal,
                        'status_lulus' => 0
                    ]
                );
            }

            toastr()->success('Berhasil Daftar');
            return redirect()->route('sidang-tugas-akhir.index');
        } catch (\Throwable $th) {
            toastr()->warning('Terdapat masalah diserver' . $th->getMessage());
            return redirect()->route('daftar-tugas-akhir.index');
        }
    }

    public function destroy($id)
    {
        try {
            $syaratTa = SyaratTa::findorfail($id);

            $fileLama = public_path('storage/syarat_ta/' . $syaratTa->dokumen_file);
            if (file_exists($fileLama)) {
                unlink($fileLama);
            }

            $syaratTa->delete();
            toastr()->success('File berhasil dihapus');
            return redirect()->route('daftar-tugas-akhir.index');
        } catch (\Throwable $th) {
            toastr()->warning('Terdapat masalah diserver' . $th->getMessage());
            return redirect()->route('daftar-tugas-akhir.index');
        }
    }
}
