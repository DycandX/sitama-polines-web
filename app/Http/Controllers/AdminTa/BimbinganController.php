<?php

namespace App\Http\Controllers\AdminTa;

use App\Http\Controllers\Controller;
use App\Models\Bimbingan;
use App\Models\BimbinganLog;
use App\Models\Dosen;
use App\Models\SyaratSidang;
use App\Models\Ta;
use App\Models\KodeProdi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

// Admin
class BimbinganController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:read_bimbingan')->only('index', 'show', 'lihat');
        $this->middleware('permission:create_bimbingan')->only('create', 'store');
        $this->middleware('permission:update_bimbingan')->only('edit', 'tambah', 'update');
        $this->middleware('permission:delete_bimbingan')->only('destroy');
    }

    public function index(Request $request)
    {
        $bimbingans = Bimbingan::all();

        $dosen = Dosen::pluck('dosen_nama', 'dosen_nip');

        $ta_mahasiswa = Bimbingan::taMahasiswaAdmin();
        $tm_collection = collect($ta_mahasiswa);
        //dd($tm_collection);
        $log = BimbinganLog::log();
        $default_ta = DB::table('master_ta')->select('ta')->where('status', 1)->first();
        //dd($default_ta);

        if ($request->filled('tahun_akademik')) {
            $tm_collection = $tm_collection->where('tahun_akademik', $request->input('tahun_akademik'));
        } else {
            $tm_collection = $tm_collection->where('tahun_akademik', $default_ta->ta);
        }

        if ($request->filled('kode_prodi')) {
            $tm_collection = $tm_collection->where('prodi_ID', $request->input('kode_prodi'));
        }

        if ($request->filled('dosen')) {
            $log->where('dosen.dosen_nama', $request->input('dosen'));
        }

        $kode_prodi = KodeProdi::all();

        $log_bimbingan = DB::select("SELECT * FROM v_dosen_aktifitas_bimbingan");
        $temp = [];
        foreach ($log_bimbingan as $row) {
            $temp[$row->dosen_nip][$row->mhs_nim] = [
                "jml_bimbingan" => $row->jml_aktivitas_pembimbingan,
                "jml_bimbingan_valid" => $row->jml_aktivitas_pembimbingan_valid,
                "jml_bimbingan_belum_valid" => $row->jml_aktivitas_pembimbingan_belum_valid,
            ];
        }
        $log_bimbingan = $temp;
        
        foreach ($tm_collection as $item) {
            $item->jumlahMasterSyarat = count($item->syarat);
            $item->jumlahSyarat = collect($item->syarat)->whereNotNull('dokumen_file')->count();
            $item->jumlahVerif = collect($item->syarat)->whereNotNull('dokumen_file')->where('verified', 1)->count();
            $item->nullSyarat = collect($item->syarat)->every(function ($s) {
                return is_null($s->dokumen_file);
            });
        }
        return view('bimbingan.index', compact('tm_collection', 'dosen', 'kode_prodi', 'default_ta', 'log_bimbingan'));
    }


    public function create()
    {
        $ta_mahasiswa = Bimbingan::ta_mahasiswa();
        $dosen = Dosen::all();
        $mhs = Bimbingan::mahasiswaAdmin();

        $taIdAda = Bimbingan::pluck('ta_id')->unique()->toArray();
        return view('bimbingan.create', compact('ta_mahasiswa', 'dosen', 'mhs', 'taIdAda'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mhs1' => 'required',
            'mhs2' => 'nullable',
            'dosen_pembimbing_1' => 'required',
            'dosen_pembimbing_2' => 'required'
        ]);

        if ($validator->fails()) {
            toastr()->error('Data Bimbingan gagal diperbarui. Periksa kembali data Anda.');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dosenPembimbing = [
            $request->dosen_pembimbing_1,
            $request->dosen_pembimbing_2
        ];

        $mhs = [
            $request->mhs1,
            $request->mhs2
        ];

        // Check if the two dosen pembimbing are the same
        if ($dosenPembimbing[0] == $dosenPembimbing[1]) {
            toastr()->warning('Dosen Pembimbing Tidak Boleh Sama');
            return redirect()->route('bimbingan.create', 'bimbingan.tambah');
        }

        // Check if the two mahasiswa are the same
        if ($mhs[0] && $mhs[0] == $mhs[1]) {
            toastr()->warning('Mahasiswa Tidak Boleh Sama');
            return redirect()->route('bimbingan.create', 'bimbingan.tambah');
        }

        foreach ($mhs as $taId) {
            // Insert records only if ta_id (mhs1 or mhs2) is not null
            if ($taId) {
                foreach ($dosenPembimbing as $index => $dosenNip) {
                    $insertDospem = new Bimbingan;
                    $insertDospem->dosen_nip = $dosenNip;
                    $insertDospem->ta_id = $taId;
                    $insertDospem->urutan = $index + 1;
                    $insertDospem->verified = '0';
                    $insertDospem->save();
                }
            }
        }

        toastr()->success('Data Bimbingan berhasil ditambahkan.');
        return redirect()->route('bimbingan.index');
    }


    public function edit($ta_id)
    {
        $ta_mahasiswa = Ta::findOrFail($ta_id);
        $dosen = Dosen::where("is_pembimbing", 1)->get();

        $bimbingan = DB::table('bimbingans')->where('ta_id', $ta_id)
            ->orderBy('urutan')
            ->get();
        $mhs = Bimbingan::mahasiswaAdmin();

        return view('bimbingan.edit', compact('ta_mahasiswa', 'dosen', 'mhs', 'bimbingan'));
    }

    public function update(Request $request, $id)
    {
        $ta = Ta::where("ta_id", $id)->first();
        $partner = DB::select("SELECT
                TAM1.mhs_nim,
                tas.ta_id
            FROM
                `tas_mahasiswa` TAM
            JOIN tas_mahasiswa TAM1 ON
                TAM.ta_id = TAM1.ta_id AND TAM.mhs_nim <> TAM1.mhs_nim
            JOIN tas ON 
                TAM1.mhs_nim = tas.mhs_nim
            WHERE
                TAM.mhs_nim = '" . $ta['mhs_nim'] . "';");

        $validator = Validator::make($request->all(), [
            'pembimbing_1' => 'required',
            'pembimbing_2' => 'required'
        ]);

        if ($validator->fails()) {
            toastr()->error('Data Bimbingan gagal diperbarui. Periksa kembali data Anda.');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dosenPembimbing = [
            $request->post('pembimbing_1'),
            $request->post('pembimbing_2')
        ];


        if ($dosenPembimbing[0] == $dosenPembimbing[1]) {
            toastr()->warning('Dosen Pembimbing Tidak Boleh Sama');
            return redirect()->route('bimbingan.index');
        }

        try {
            $update_ta = Ta::findOrFail($id);
            $update_ta->mhs_nim = $request->post('mhs_nim');
            $update_ta->ta_judul = $request->post('ta_judul');
            $update_ta->tahun_akademik = $request->post('tahun_akademik');
            $update_ta->update();

            foreach ($dosenPembimbing as $index => $dosenNip) {
                $updateDospem = DB::table('bimbingans')
                    ->updateOrInsert(
                        [
                            'ta_id' => $id,
                            'urutan' => $index + 1
                        ],
                        [
                            'dosen_nip' => $dosenNip,
                            'verified' => '0'
                        ]
                    );
                if (isset($partner)) {
                    foreach ($partner as $row) {
                        $updateDospem = DB::table('bimbingans')
                            ->updateOrInsert(
                                [
                                    'ta_id' => $row->ta_id,
                                    'urutan' => $index + 1
                                ],
                                [
                                    'dosen_nip' => $dosenNip,
                                    'verified' => '0'
                                ]
                            );
                    }
                }
            }

            toastr()->success('Data bimbingan berhasil diperbarui');
            return redirect()->route('bimbingan.index');
        } catch (\Throwable $th) {
            toastr()->error('Terjadi masalah pada server. Data Bimbingan gagal diperbarui.' . $th->getMessage());
            return redirect()->route('bimbingan.index');
        }
    }


    public function destroy($id)
    {
        try {
            // Menggunakan transaksi untuk menjaga integritas data
            DB::transaction(function () use ($id) {
                // Hapus dulu semua entri Bimbingan terkait ta_id yang akan dihapus
                Bimbingan::where('ta_id', $id)->delete();

                // Setelah itu baru hapus entri di Ta
                Ta::findOrFail($id)->delete();
            });

            toastr()->success('Data Bimbingan berhasil dihapus.');
            return redirect()->route('bimbingan.index');
        } catch (\Throwable $th) {
            toastr()->error('Terjadi masalah pada server. Data Bimbingan gagal dihapus. Pesan Kesalahan: ' . $th->getMessage());
            return redirect()->route('bimbingan.index');
        }
    }

    public function bimblog(Request $request, $ta_id)
    {
        $ta_mahasiswa = Ta::detailMahasiswa($ta_id);

        $log = collect(BimbinganLog::log())->where('ta_id', $ta_id);
        $logJumlah = collect(BimbinganLog::log())->where('ta_id', $ta_id);

        $masterJumlah = DB::table('bimbingan_counts')->value('bimbingan_counts.total_bimbingan');

        if ($request->filled('pembimbing')) {
            $log = $log->where('dosen_nip', $request->pembimbing);
        }

        return view('bimbingan.bimbinganLog', compact('ta_mahasiswa', 'log', 'masterJumlah', 'logJumlah'));
    }


    public function show($ta_id)
    {
        $ta_mahasiswa = Ta::detailMahasiswa($ta_id);
        $bimbingans = Bimbingan::all();
        $dosen = Dosen::all();
        $syarat = SyaratSidang::syaratMahasiswa($ta_id);

        $verifiedAll = $syarat->every(function ($item) {
            return $item->verified == 1;
        });

        $nullSyarat = $syarat->every(function ($item) {
            return is_null($item->dokumen_file);
        });

        return view('bimbingan.upload_sk', compact('bimbingans', 'dosen', 'ta_mahasiswa', 'syarat', 'verifiedAll', 'nullSyarat'));
    }

    public function verifyAll(Request $request, $ta_id)
    {
        if (isset($request->syaratCheck)) {
            foreach ($request->syaratCheck as $dokumenId) {
                SyaratSidang::where('ta_id', $ta_id)
                    ->where('dokumen_id', $dokumenId)
                    ->update([
                        'verified' => '1'
                    ]);
            }
            toastr()->success('Berhasil diverifikasi');
            return redirect()->route('bimbingan.show', $ta_id);
        } else {
            toastr()->error('Terdapat masalah');
            return redirect()->route('bimbingan.show', $ta_id);
        }
    }

    public function verifySingle($syarat_sidang_id)
    {
        $valid = request()->get('valid');

        $affected = DB::table('syarat_sidang')
            ->where('syarat_sidang_id', $syarat_sidang_id)
            ->update(['verified' => $valid]);

        echo $valid;
    }
}
