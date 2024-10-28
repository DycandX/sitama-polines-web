<?php

namespace App\Http\Controllers\AdminTa;

use App\Http\Controllers\Controller;
use App\Models\Bimbingan;
use App\Models\Ta;
use App\Models\KodeProdi;
use App\Models\Nilai;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

// Admin
class TaController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:read_ta')->only('index', 'show', 'lihat');
        $this->middleware('permission:create_ta')->only('create', 'store');
        $this->middleware('permission:update_ta')->only('edit', 'tambah', 'update');
        $this->middleware('permission:delete_ta')->only('destroy');
    }

    public function index(Request $request)
    {
        $kode_prodi = KodeProdi::all();

        $taSidang = Ta::taSidang2();

        $taSidangQuery = collect($taSidang);

        if ($request->filled('tahun_akademik')) {
            $taSidangQuery = $taSidangQuery->where('tahun_akademik', $request->input('tahun_akademik'));
        }

        if ($request->filled('kode_prodi')) {
            $taSidangQuery = $taSidangQuery->where('prodi_ID', $request->input('kode_prodi'));
        }

        $taSidang = $taSidangQuery->all();
        $kode_prodi = KodeProdi::all();
        //dd($kode_prodi);
        return view('ta.index', compact('taSidang', 'kode_prodi'));
    }


    public function updateOrInsertStatusLulus(Request $request, $ta_sidang_id)
    {
        try {
            DB::table('ta_sidang')->updateOrInsert(
                [
                    'ta_sidang_id' => $ta_sidang_id
                ],
                [
                    'status_lulus' => $request->input('statusLulus')
                ]
            );
            return redirect()->route('ujian-sidang.index')->with('success', 'Status lulus berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('ujian-sidang.index')->withErrors(['error' => 'Gagal memperbarui status lulus', 'details' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                DB::table('penilaian_penguji_detail')->whereIn('dosen_nip', function ($query) use ($id) {
                    $query->select('dosen_nip')
                        ->from('penilaian_penguji')
                        ->where('ta_sidang_id', $id);
                })->delete();

                DB::table('penilaian_penguji')->where('ta_sidang_id', $id)->delete();
                DB::table('ta_sidang')->where('ta_sidang_id', $id)->delete();
            });

            toastr()->success('Data Sidang TA berhasil dihapus.');
            return redirect()->route('ta.index');
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Failed to delete data', 'details' => $th->getMessage()], 500);
        }
    }

    public function statistikPenguji()
    {
        $tahun_akademik = request()->get('tahun_akademik') ? request()->get('tahun_akademik') : (date("Y") - 1) . "/" . date("Y");

        $dosenList = new Collection(DB::select("SELECT * FROM v_statistik_menguji WHERE tahun_akademik_merge = '" . $tahun_akademik . "'"));

        return view('ta.statistik', compact('dosenList'));
    }

    public function editPenguji($taSidangId)
    {
        // Fetching necessary data
        $taSidang = DB::table('ta_sidang')
            ->where('ta_sidang_id', $taSidangId)
            ->first();
        

        $pembimbingResult = DB::table('bimbingans')
            ->select("dosen_nip")
            ->where('ta_id', $taSidang->ta_id)
            ->get();

        $pembimbing = [];
        foreach ($pembimbingResult as $row) {
            $pembimbing[] = $row->dosen_nip;
        }

        $dosenList = new Collection(DB::select("SELECT
                    D.dosen_nip,
                    D.dosen_nama,
                    D.is_penguji_utama,
                    D.is_penguji,
                    D.is_sekretaris,
                    COUNT(PP.dosen_nip) jml_menguji,
                    COUNT(TS.dosen_nip) jml_sekretaris
                FROM
                    dosen D
                    LEFT JOIN penilaian_penguji PP
                        ON D.dosen_nip = PP.dosen_nip
                    LEFT JOIN ta_sidang TS
                        ON D.dosen_nip = TS.dosen_nip
                WHERE D.dosen_nip NOT IN ('" . implode("','", $pembimbing) . "')
                GROUP BY 
                    D.dosen_nip,
                    D.dosen_nama, 
                    D.is_penguji_utama, 
                    D.is_penguji, 
                    D.is_sekretaris
                ORDER BY jml_menguji ASC, jml_sekretaris ASC, D.dosen_nip ASC;"));

        //$dosenListPengujiUtama = $dosenList->where("is_penguji_utama", 1)->where('is_penguji', 1)->values();
        $dosenListPenguji = $dosenList->where("is_penguji_utama", 0)->where('is_penguji', 1)->values();
        $dosenListSekretaris = $dosenList->where("is_sekretaris", 1)->values();        

        $penguji = DB::table('penilaian_penguji')
            ->where('ta_sidang_id', $taSidangId)
            ->get()
            ->keyBy('urutan');
        $sekretaris = '';

        // dd($dosenListPenguji);

        return view('ta.penguji', compact('taSidang', 'dosenList', 'penguji', 'sekretaris', 'dosenListPenguji', 'dosenListSekretaris'));
    }

    public function updatePenguji(Request $request, $taSidangId)
    {
        $request->validate([
            'penguji_1' => 'nullable|string',
            'penguji_2' => 'nullable|string',
            'penguji_3' => 'nullable|string',
            'sekretaris' => 'nullable|string',
        ]);

        $pembimbing = DB::table('bimbingans')
            ->join('ta_sidang', 'ta_sidang.ta_id', '=', 'bimbingans.ta_id')
            ->select(
                DB::raw('GROUP_CONCAT(distinct(bimbingans.dosen_nip) ORDER BY bimbingans.bimbingan_id) as dosen_nip')
            )
            ->where('ta_sidang.ta_sidang_id', $taSidangId)
            ->first();

        $dosen_nip_array = explode(',', $pembimbing->dosen_nip);
        $pembimbing1 = $dosen_nip_array[0];
        $pembimbing2 = $dosen_nip_array[1] ?? null;

        $penguji1 = $request->input('penguji_1');
        $penguji2 = $request->input('penguji_2');
        $penguji3 = $request->input('penguji_3');
        $sekretaris = $request->input('sekretaris');

        // Check if any penguji are the same or same as pembimbing
        if (($penguji1 && ($penguji1 == $penguji2 || $penguji1 == $penguji3 || $penguji1 == $pembimbing1 || $penguji1 == $pembimbing2)) ||
            ($penguji2 && ($penguji2 == $penguji3 || $penguji2 == $pembimbing1 || $penguji2 == $pembimbing2)) ||
            ($penguji3 && ($penguji3 == $pembimbing1 || $penguji3 == $pembimbing2))
        ) {
            toastr()->error('Penguji cannot be the same as another Penguji or Pembimbing.');
            return redirect()->route('ta.index');
        }

        // Check if sekretaris is same as any penguji or pembimbing
        if ($sekretaris && ($sekretaris == $penguji1 || $sekretaris == $penguji2 || $sekretaris == $penguji3 || $sekretaris == $pembimbing1 || $sekretaris == $pembimbing2)) {
            toastr()->error('Sekretaris tidak boleh sama dengan salah satu dari Penguji atau Pembimbing.');
            return redirect()->route('ta.index');
        }

        //GET DATA PARTNER
        $partner = DB::selectOne("SELECT
                        TAM1.mhs_nim,
                        tas.ta_id,
                        TASS.ta_sidang_id
                    FROM
                        `tas_mahasiswa` TAM
                    JOIN tas_mahasiswa TAM1 ON
                        TAM.ta_id = TAM1.ta_id 
                        AND TAM.mhs_nim <> TAM1.mhs_nim
                    JOIN tas 
                        ON TAM1.mhs_nim = tas.mhs_nim
                    JOIN ta_sidang TASS
                        ON tas.ta_id = TASS.ta_id
                    WHERE TAM.mhs_nim = (SELECT
                                                mhs_nim
                                            FROM
                                                `ta_sidang`
                                            JOIN tas
                                                ON tas.ta_id = ta_sidang.ta_id
                                            WHERE
                                                ta_sidang.ta_sidang_id = '" . $taSidangId . "');");

        DB::beginTransaction();
        try {
            // Update penguji 1
            if ($penguji1) {
                DB::table('penilaian_penguji')
                    ->updateOrInsert(
                        ['ta_sidang_id' => $taSidangId, 'urutan' => 1],
                        ['dosen_nip' => $penguji1]
                    );
                toastr()->success('Plotting Tim Penguji berhasil.');
            }

            // Update penguji 2
            if ($penguji2) {
                DB::table('penilaian_penguji')
                    ->updateOrInsert(
                        ['ta_sidang_id' => $taSidangId, 'urutan' => 2],
                        ['dosen_nip' => $penguji2]
                    );
                toastr()->success('Plotting Tim Penguji berhasil.');
            }

            // Update penguji 3
            if ($penguji3) {
                DB::table('penilaian_penguji')
                    ->updateOrInsert(
                        ['ta_sidang_id' => $taSidangId, 'urutan' => 3],
                        ['dosen_nip' => $penguji3]
                    );
                toastr()->success('Plotting Tim Penguji berhasil.');
            }

            // Update or insert sekretaris if not same as any penguji
            if ($sekretaris) {
                DB::table('ta_sidang')
                    ->updateOrInsert(
                        ['ta_sidang_id' => $taSidangId],
                        ['dosen_nip' => $sekretaris]
                    );
                toastr()->success('Plotting Tim Penguji berhasil.');
            }

            if ($partner) {
                // Update penguji 1
                if ($penguji1) {
                    DB::table('penilaian_penguji')
                        ->updateOrInsert(
                            ['ta_sidang_id' => $partner->ta_sidang_id, 'urutan' => 1],
                            ['dosen_nip' => $penguji1]
                        );
                    toastr()->success('Plotting Tim Penguji berhasil.');
                }

                // Update penguji 2
                if ($penguji2) {
                    DB::table('penilaian_penguji')
                        ->updateOrInsert(
                            ['ta_sidang_id' => $partner->ta_sidang_id, 'urutan' => 2],
                            ['dosen_nip' => $penguji2]
                        );
                    toastr()->success('Plotting Tim Penguji berhasil.');
                }

                // Update penguji 3
                if ($penguji3) {
                    DB::table('penilaian_penguji')
                        ->updateOrInsert(
                            ['ta_sidang_id' => $partner->ta_sidang_id, 'urutan' => 3],
                            ['dosen_nip' => $penguji3]
                        );
                    toastr()->success('Plotting Tim Penguji berhasil.');
                }

                // Update or insert sekretaris if not same as any penguji
                if ($sekretaris) {
                    DB::table('ta_sidang')
                        ->updateOrInsert(
                            ['ta_sidang_id' => $partner->ta_sidang_id],
                            ['dosen_nip' => $sekretaris]
                        );
                    toastr()->success('Plotting Tim Penguji berhasil.');
                }
            }

            DB::commit();
            return redirect()->route('ta.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal memperbarui penguji', 'details' => $e->getMessage()], 500);
        }
    }



    public function show($ta_sidang_id)
    {
        $back = isset($_GET['back']) ? $_GET['back'] : "/ta";
        $penilaian = Nilai::penilaian($ta_sidang_id);
        $penguji = Nilai::penguji($ta_sidang_id);
        $pembimbing = Nilai::pembimbing($ta_sidang_id);

        $unsur_pembimbing = count(DB::table("unsur_nilai_pembimbing")->get());
        $unsur_penguji = count(DB::table("unsur_nilai_penguji")->get());

        return view('ta.nilai', compact('penilaian', 'penguji', 'pembimbing', 'unsur_pembimbing', 'unsur_penguji', 'back'));
    }

    public function CetakSuratTugasAdmin($id)
    {
        //cari prodi dari nim
        $datamhs = Ta::where('ta_id', $id)->first();
        $nim = $datamhs->mhs_nim;
        $depan_jenjang = substr($nim, 0, 1);
        if ($depan_jenjang == '3')
            $jen = "D3";
        elseif ($depan_jenjang == '4')
            $jen = "D4";

        $belakang_jenjang = substr($nim, 1, 2);
        if ($belakang_jenjang == '33') {
            $prod = "TI";
            $kelasnya = "4";
        } elseif ($belakang_jenjang == '34') {
            $prod = "IK";
            $kelasnya = "3";
        }

        $kode_prodi = $jen . $prod;

        $detailta = Ta::detailMahasiswa($id);

        //apakah mahasiswa berkelompok
        $taid = Ta::TaId($nim);
        $iskelompok = Ta::IsKelompok($taid->ta_id);
        $satukelompok = Ta::SatuKelompok($taid->ta_id);
        // dd($satukelompok);

        //info ujian
        $infoujian = Ta::dataujian($id);
        // dd($infoujian);
        $tanggal_sidang = $infoujian->tgl_sidang;
        $hari = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
        $nama_hari_inggris = date('l', strtotime($tanggal_sidang));
        $hari_indonesia = $hari[array_search($nama_hari_inggris, array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'))];
        $tanggal_dmy = date('d-m-Y', strtotime($tanggal_sidang));
        list($tanggal, $bulan, $tahun) = explode('-', $tanggal_dmy);
        $bulan_indonesia = array(
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        );
        $nama_bulan = $bulan_indonesia[$bulan];
        $tanggal_indonesia = $tanggal . ' ' . $nama_bulan . ' ' . $tahun;


        //ambil tanggal sekarang
        $tanggal_sekarang = date('d-m-Y', strtotime(date("Y-m-d")));
        list($tanggal2, $bulan2, $tahun2) = explode('-', $tanggal_sekarang);
        $nama_bulan2 = $bulan_indonesia[$bulan2];
        $tanggal_now = $tanggal2 . ' ' . $nama_bulan2 . ' ' . $tahun2;

        if ($hari_indonesia == 'Jumat') {
            $sesi_mulai = $infoujian->sesi_waktu_mulai_jumat;
            $sesi_selesai = $infoujian->sesi_waktu_selesai_jumat;
        } else {
            $sesi_mulai = $infoujian->sesi_waktu_mulai;
            $sesi_selesai = $infoujian->sesi_waktu_selesai;
        }


        // Ambil tahun_ajaran yang aktif
        $tahunAjaran = Ta::CekTahunAjaran();

        if (!$tahunAjaran) {
            return redirect()->route('ta.index')->with('error', 'Tahun ajaran aktif tidak ditemukan.');
        }

        // Ambil no_sk dan file_ttd berdasarkan tahun_ajaran
        $noSk = Ta::NoSk($tahunAjaran->ta, $kode_prodi);

        if (!$noSk) {
            return redirect()->route('ta.index')->with('error', 'Nomor SK tidak ditemukan.');
        }

        // $pdf = new CustomPdfAdmin('P', 'mm', 'A4');
        $pdf = new CustomPdfAdmin('P', 'mm', [210, 330]);

        $pdf->AddPage();

        $imagePath = public_path('dist/img/kop_polines.png');

        $pdf->Image($imagePath, 10, 10, 190);

        $pdf->SetFont('Arial', 'B', 12);

        $pdf->Ln(45);

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetWidths([40, 110, 40]);

        $data = [
            'Jurusan T.Elektro POLINES',
            "SURAT TUGAS\n" . $noSk->no_sk,
            ''
        ];

        $underline = [0, 1, 0];
        $pdf->Row($data, $underline);

        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Ketua Jurusan Teknik Elektro memberi tugas kepada Tim Penguji Tugas Akhir berikut ini :', 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetWidths([10, 40, 90, 50]);

        $header = ['No.', 'JABATAN', 'NAMA PENGUJI', 'NIP'];
        $pdf->Row($header);
        $pdf->SetFont('Arial', '', 12);
        $tableData = [
            ['1', ' Ketua Penguji', ' ' . $detailta->dosen[0]["dosen_nama"], '198810142019031007'],
            ['2', ' Sekretaris', ' ' . $infoujian->sekretaris_nama, $infoujian->sekretaris_nip],
            ['3', ' Penguji 1', ' ' . $infoujian->penguji1_nama, $infoujian->penguji1_nip],
            ['4', ' Penguji 2', ' ' . $infoujian->penguji2_nama, $infoujian->penguji2_nip],
            ['5', ' Penguji 3', ' ' . $infoujian->penguji3_nama, $infoujian->penguji3_nip],
        ];

        foreach ($tableData as $row) {
            $pdf->Cell(10, 7, $row[0], 1, 0, 'C');
            $pdf->Cell(40, 7, $row[1], 1, 0, 'L');
            $pdf->Cell(90, 7, $row[2], 1, 0, 'L');
            $pdf->Cell(50, 7, $row[3], 1, 1, 'C');
        }

        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Untuk menguji mahasiswa tersebut :', 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetWidths([10, 90, 40, 50]);

        $header3 = ['No.', 'NAMA', 'NIM', 'KELAS'];
        $pdf->Row($header3);
        $pdf->SetFont('Arial', '', 12);

        if ($iskelompok == '1') {
            $depan_kelas = substr($detailta->mhs_nim, 5, 1);
            if ($depan_kelas == 0) {
                $kelas = 'A';
            } elseif ($depan_kelas == 1) {
                $kelas = 'B';
            } elseif ($depan_kelas == 2) {
                $kelas = 'C';
            } elseif ($depan_kelas == 3) {
                $kelas = 'D';
            } elseif ($depan_kelas == 4) {
                $kelas = 'E';
            } elseif ($depan_kelas == 5) {
                $kelas = 'F';
            } elseif ($depan_kelas == 6) {
                $kelas = 'G';
            } elseif ($depan_kelas == 7) {
                $kelas = 'H';
            }
            $tableData3 = [
                ['1', ' ' . $detailta->mhs_nama, $detailta->mhs_nim, $prod . "-" . $kelasnya . $kelas],
            ];
        } elseif ($iskelompok == '2') {
            foreach ($satukelompok as $satu) {
            }
            $tableData3 = [];
            $no = 1;
            foreach ($satukelompok as $index) {
                //penentuan kelas
                $depan_kelas = substr($index->mhs_nim, 5, 1);
                if ($depan_kelas == 0) {
                    $kelas = 'A';
                } elseif ($depan_kelas == 1) {
                    $kelas = 'B';
                } elseif ($depan_kelas == 2) {
                    $kelas = 'C';
                } elseif ($depan_kelas == 3) {
                    $kelas = 'D';
                } elseif ($depan_kelas == 4) {
                    $kelas = 'E';
                } elseif ($depan_kelas == 5) {
                    $kelas = 'F';
                } elseif ($depan_kelas == 6) {
                    $kelas = 'G';
                } elseif ($depan_kelas == 7) {
                    $kelas = 'H';
                }

                $tableData3[] = [
                    $no++, // Nomor urut
                    ' ' . $index->mhs_nama,
                    $index->mhs_nim,
                    $prod . "-" . $kelasnya . $kelas,
                ];
            }

            // $tableData3 = [
            //     ['1', ' '.$detailta->mhs_nama, '12345678', 'Kelas A'],
            //     ['2', ' '.'Mahasiswa B', '87654321', 'Kelas B'],
            // ];
        }

        foreach ($tableData3 as $row) {
            $pdf->Cell(10, 7, $row[0], 1, 0, 'C');
            $pdf->Cell(90, 7, $row[1], 1, 0, 'L');
            $pdf->Cell(40, 7, $row[2], 1, 0, 'C');
            $pdf->Cell(50, 7, $row[3], 1, 1, 'C'); // End of line
        }

        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'yang didampingi Pembimbing sebagai berikut :', 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetWidths([10, 40, 90, 50]);

        $header4 = ['No.', 'JABATAN', 'NAMA PEMBIMBING', 'NIP'];
        $pdf->Row($header4);
        $pdf->SetFont('Arial', '', 12);
        $tableData4 = [
            ['1', ' ' . 'Pembimbing 1', ' ' . $detailta->dosen[0]["dosen_nama"], $detailta->dosen[0]["dosen_nip"]],
            ['2', ' ' . 'Pembimbing 2', ' ' . $detailta->dosen[1]["dosen_nama"], $detailta->dosen[1]["dosen_nip"]],
        ];


        foreach ($tableData4 as $row) {
            $pdf->Cell(10, 7, $row[0], 1, 0, 'C');
            $pdf->Cell(40, 7, $row[1], 1, 0, 'L');
            $pdf->Cell(90, 7, $row[2], 1, 0, 'L');
            $pdf->Cell(50, 7, $row[3], 1, 1, 'C'); // End of line
        }

        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'Dengan Judul Tugas Akhir :', 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->MultiCell(0, 5, $infoujian->judul_final, 0, '');

        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(5);
        $pdf->Cell(0, 5, 'Yang dilaksanakan pada waktu sebagai berikut :', 0, 1);

        $pdf->Cell(15, 6, '', 0, 0);
        $pdf->Cell(50, 6, 'Hari, Tanggal', 0, 0);
        $pdf->Cell(0, 6, ": " . $hari_indonesia . " / " . $tanggal_indonesia, 0, 1);
        $pdf->Cell(15, 6, '', 0, 0);
        $pdf->Cell(50, 6, 'Waktu', 0, 0);
        $pdf->Cell(0, 6, ": " . $sesi_mulai . ' - ' . $sesi_selesai, 0, 1);
        $pdf->Cell(15, 6, '', 0, 0);
        $pdf->Cell(50, 6, 'Tempat', 0, 0);
        $pdf->Cell(0, 6, ": " . $infoujian->ruangan_nama, 0, 1);
        $pdf->Cell(0, 7, 'Tugas ini supaya dijalankan dengan sebaik-baiknya dan hasilnya dilaporkan kepada Jurusan.', 0, 1);

        $pdf->Ln(3);
        $pdf->Cell(120, 5, '', 0, 0);
        $pdf->Cell(0, 5, 'Semarang,' . $tanggal_now, 0, 1);

        //ketua jurusan saja
        // $pdf->Cell(120, 5, '', 0, 0);
        // $pdf->Cell(0, 5, 'Ketua Jurusan Teknik Elektro', 0, 1);

        //an ketua jurusan, sekretaris jurusan
        $pdf->Cell(120, 5, '', 0, 0);
        $pdf->Cell(0, 5, 'a.n Ketua Jurusan Teknik Elektro', 0, 1);
        $pdf->Cell(120, 5, '', 0, 0);
        $pdf->Cell(0, 5, 'Sekretaris Jurusan Teknik Elektro', 0, 1);

        $pdf->Ln(20);
        $signaturePath = public_path('dist/img/' . $noSk->file_paraf);
        // $pdf->Image($signaturePath, $pdf->GetX() + 110, $pdf->GetY(), 10, 10);
        $pdf->Image($signaturePath, $pdf->GetX() + 140, $pdf->GetY() - 17, 15, 15);
        $pdf->Cell(120, 5, '', 0, 0);
        $pdf->Cell(0, 5, $noSk->nama_kajur, 0, 1);
        $pdf->Cell(120, 5, '', 0, 0);
        $pdf->Cell(0, 5, 'NIP. ' . $noSk->nip_kajur, 0, 1);

        $pdf->SetY(-32);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 5, 'Diserahkan ke Tim Penguji Tugas Akhir (lima orang) dan Pembimbing paling lambat tiga hari sebelum ujian, beserta naskah Tugas Akhir untuk Penguji.', 1, 'C');


        return response($pdf->Output('S'), 200)->header('Content-Type', 'application/pdf');
    }
}
