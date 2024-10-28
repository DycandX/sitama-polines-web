<?php

namespace App\Http\Controllers\DosenTa;

use App\Http\Controllers\Controller;
use App\Models\Bimbingan;
use Illuminate\Http\Request;
use App\Models\MahasiswaBimbingan;
use App\Models\BimbinganLog;
use App\Models\KodeProdi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Dosen
class MahasiswaBimbinganController extends Controller
{
    public function index(Request $request)
    {
        $ta_mahasiswa = collect(MahasiswaBimbingan::ta_mahasiswa());

        $default_ta = DB::table('master_ta')->select('ta')->where('status', 1)->first();
        if ($request->filled('akademik')) {
            $ta_mahasiswa = $ta_mahasiswa->where('tahun_akademik', $request->input('akademik'));
        } else {
            $ta_mahasiswa = $ta_mahasiswa->where('tahun_akademik', $default_ta->ta);
        }

        if ($request->filled('prodi')) {
            $ta_mahasiswa = $ta_mahasiswa->where('prodi_ID', $request->input('prodi'));
        }

        $masterJumlah = DB::table('bimbingan_counts')->value('bimbingan_counts.total_bimbingan');
        $kode_prodi = KodeProdi::all();

        return view('mhsbimbingan.index', compact('ta_mahasiswa', 'kode_prodi', 'default_ta', 'masterJumlah'));
    }

    public function pembimbingan(Request $request, $ta_id)
    {
        $mahasiswa = collect(Bimbingan::ta_mahasiswa2())->where('ta_id', $ta_id)->first();

        if (is_null($mahasiswa)) {
            toastr()->warning('Mahasiswa not found for the given TA ID');
            return redirect()->route('mhsbimbingan.index');
        }

        $bimbLog = collect(BimbinganLog::bimbinganLogDosen($ta_id));
        $bimbLogJumlah = collect(BimbinganLog::bimbinganLogDosen($ta_id));

        Carbon::setLocale('id');
        foreach ($bimbLog as $item) {
            $item->format_tanggal = Carbon::parse($item->bimb_tgl)->translatedFormat('l, j F Y');
        }

        $masterJumlah = DB::table('bimbingan_counts')->value('bimbingan_counts.total_bimbingan');

        return view('mhsbimbingan.pembimbingan', compact('bimbLog', 'mahasiswa', 'masterJumlah', 'bimbLogJumlah'));
    }

    public function setujuiSidangAkhir(Request $request, $ta_id)
    {
        $verif = BimbinganLog::bimbinganLogDosen($ta_id)->where('bimb_status', 1)->count();
        $masterJumlah = DB::table('bimbingan_counts')
            ->value('bimbingan_counts.total_bimbingan');

        if ($verif >= $masterJumlah) {

            if (isset($request)) {
                DB::table('bimbingans')
                    ->where('ta_id', $ta_id)
                    ->where('urutan', $request->urutan)
                    ->update(
                        [
                            'verified' => '1'
                        ],
                    );
            }

            toastr()->success('Berhasil di verifikasi');
            return redirect()->route('mhsbimbingan.index');
        } else {
            toastr()->error('Bimbingan belum terpenuhi');
            return redirect()->route('mhsbimbingan.index');
        }
    }

    public function setujuiPembimbingan($bimbingan_log_id)
    {
        try {
            $bimbinganLog = BimbinganLog::findOrFail($bimbingan_log_id);
            $bimbinganLog->bimb_status = 1;
            $bimbinganLog->save();

            $taId = DB::table('bimbingan_log')
                ->join('bimbingans', 'bimbingans.bimbingan_id', '=', 'bimbingan_log.bimbingan_id')
                ->select('bimbingans.ta_id')
                ->where('bimbingan_log.bimbingan_log_id', $bimbingan_log_id)
                ->first();

            toastr()->success('Berhasil diverifikasi');
            return redirect()->route('mhsbimbingan.pembimbingan', $taId->ta_id);
        } catch (\Exception $e) {
            toastr()->error('Ada masalah di server');
            return redirect()->route('mhsbimbingan.index');
        }
    }
}
