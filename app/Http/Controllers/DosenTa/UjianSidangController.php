<?php

namespace App\Http\Controllers\DosenTa;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\KodeProdi;
use App\Models\Ta;
use App\Models\TaSidang;
use Illuminate\Http\Request;
use App\Models\UjianSidang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

// Dosen
class UjianSidangController extends Controller
{
    public function index(Request $request)
    {
        $ta_mahasiswa = UjianSidang::ta_mahasiswa();

        Carbon::setLocale('id');

        $userNip = Dosen::dosenNip()->dosen_nip;

        foreach ($ta_mahasiswa as $item) {
            $item->format_tanggal = Carbon::parse($item->tgl_sidang)->translatedFormat('l, j F Y');

            $item->isPembimbing = collect($item->user_dosen)->contains(function ($pembimbing) use ($userNip) {
                return $pembimbing['user_dosen_nip'] == $userNip;
            });

            $item->isPenguji = collect($item->user_dosen_penguji)->contains(function ($penguji) use ($userNip) {
                return $penguji['user_dosen_nip'] == $userNip;
            });

            $item->isSekre = !empty($item->sekre_nip);

            $item->emptyPenguji = collect($item->user_dosen_penguji)->contains(function ($dosen) {
                return empty($dosen['user_dosen_nip']);
            });

            // Tambahkan logika waktu berdasarkan hari sidang
            $hari = Carbon::parse($item->tgl_sidang)->dayOfWeek;
            if ($hari == 5) { // Jika hari Jumat
                $item->waktu_mulai = $item->sesi_waktu_mulai_jumat;
                $item->waktu_selesai = $item->sesi_waktu_selesai_jumat;
            } else {
                $item->waktu_mulai = $item->sesi_waktu_mulai;
                $item->waktu_selesai = $item->sesi_waktu_selesai;
            }
        }

        if ($request->filled('akademik')) {
            $ta_mahasiswa = collect($ta_mahasiswa)->where('tahun_akademik', $request->input('akademik'));
        }

        if ($request->filled('prodi')) {
            $ta_mahasiswa = collect($ta_mahasiswa)->where('prodi_ID', $request->input('prodi'));
        }

        $kode_prodi = KodeProdi::all();

        return view('ujian-sidang.index', compact('ta_mahasiswa', 'userNip', 'kode_prodi'));
    }

    public function kelayakan($ta_id)
    {

        $ta_mahasiswa = Ta::findOrFail($ta_id);
        $infoMhs = Ta::detailKelayakan($ta_id);
        $nilai_pembimbing = Ta::unsur_nilai_pembimbing()->get();
        $nilai_penguji = Ta::unsur_nilai_penguji();

        $ta_sidang = TaSidang::where("ta_id", $ta_id)->first();
        $userNip = Dosen::dosenNip()->dosen_nip;

        $nilai_pembimbing_saved = DB::select("SELECT * FROM penilaian_pembimbing WHERE ta_sidang_id = '" . $ta_sidang->ta_sidang_id . "' AND dosen_nip = $userNip");

        $temp = [];
        foreach ($nilai_pembimbing_saved as $row) {
            $temp[$row->nilai_id] = $row->berinilai;
        }
        $nilai_pembimbing_saved = $temp;

        return view('ujian-sidang.kelayakan', compact('ta_mahasiswa', 'infoMhs', 'nilai_pembimbing', 'nilai_pembimbing_saved'));
    }


    public function penguji($ta_id)
    {

        $ta_mahasiswa = Ta::findOrFail($ta_id);
        $infoMhs = Ta::detailKelayakan2($ta_id);
        $nilai_penguji = Ta::unsur_nilai_penguji()->get();

        $ta_sidang = TaSidang::where("ta_id", $ta_id)->first();
        $userNip = Dosen::dosenNip()->dosen_nip;

        $nilai_penguji_saved = DB::select("SELECT * FROM penilaian_penguji_detail WHERE ta_sidang_id = '" . $ta_sidang->ta_sidang_id . "' AND dosen_nip = $userNip");
        $temp = [];
        foreach ($nilai_penguji_saved as $row) {
            $temp[$row->nilai_id] = $row->berinilai;
        }
        $nilai_penguji_saved = $temp;

        return view('ujian-sidang.penguji', compact('ta_mahasiswa', 'infoMhs', 'nilai_penguji', 'nilai_penguji_saved'));
    }

    public function storeKelayakan(Request $request, $taSidangId)
    {
        $nip = Dosen::dosenNip()->dosen_nip;

        $nilai_pembimbing = Ta::unsur_nilai_pembimbing()->get();

        foreach ($nilai_pembimbing as $index => $nilai) {

            DB::table('penilaian_pembimbing')->updateOrInsert(

                [
                    'ta_sidang_id' => $taSidangId,
                    'nilai_id' => $request->nilaiId[$index],
                    'dosen_nip' => $nip,
                ],
                [
                    'berinilai' => $request->unsur[$nilai->nilai_id]
                ]
            );
        };

        $nilaiPembimbingAll = DB::table('penilaian_pembimbing')
            ->join('unsur_nilai_pembimbing', 'unsur_nilai_pembimbing.nilai_id', '=', 'penilaian_pembimbing.nilai_id')
            ->where('penilaian_pembimbing.ta_sidang_id', $taSidangId)
            ->select(
                'penilaian_pembimbing.dosen_nip',
                'penilaian_pembimbing.berinilai',
                'unsur_nilai_pembimbing.bobot',
            )
            ->get();

        $pembimbingJumlah = DB::table('bimbingans')
            ->join('ta_sidang', 'ta_sidang.ta_id', '=', 'bimbingans.ta_id')
            ->where('ta_sidang.ta_sidang_id', $taSidangId)
            ->count();

        $nilaiBobot = 0;
        foreach ($nilaiPembimbingAll as $nilai) {
            $nilaiBobot += $nilai->berinilai * $nilai->bobot;
        }
        $rataRata = $nilaiBobot / $pembimbingJumlah;

        DB::table('ta_sidang')
            ->where('ta_sidang_id', $taSidangId)
            ->update(
                [
                    'nilai_pembimbing' => $rataRata,
                ],
            );

        $taSidang = DB::table('ta_sidang')
            ->where('ta_sidang_id', $taSidangId)
            ->first();

        $nilaiPembimbing = $taSidang->nilai_pembimbing ?? 0;
        $nilaiPenguji = $taSidang->nilai_penguji ?? 0;

        $nilaiAkhir = ($nilaiPembimbing + $nilaiPenguji);

        DB::table('ta_sidang')
            ->where('ta_sidang_id', $taSidangId)
            ->update(
                [
                    'nilai_akhir' => $nilaiAkhir
                ],
            );

        return redirect()->route('ujian-sidang.index')->with('success', 'Nilai berhasil disimpan.');
    }



    public function storePenguji(Request $request, $taSidangId)
    {
        $nilai_penguji = Ta::unsur_nilai_penguji()->get();

        $nip = Dosen::dosenNip()->dosen_nip;

        foreach ($nilai_penguji as $index => $nilai) {
            DB::table('penilaian_penguji_detail')->updateOrInsert(

                [
                    'ta_sidang_id' => $taSidangId,
                    'nilai_id' => $request->nilaiId[$index],
                    'dosen_nip' => $nip,
                ],
                [
                    'berinilai' => $request->unsur[$nilai->nilai_id]
                ]
            );
        };

        $nilaiPengujiAll = DB::table('penilaian_penguji_detail')
            ->join('unsur_nilai_penguji', 'unsur_nilai_penguji.nilai_id', '=', 'penilaian_penguji_detail.nilai_id')
            ->where('penilaian_penguji_detail.ta_sidang_id', $taSidangId)
            ->select(
                'penilaian_penguji_detail.dosen_nip',
                'penilaian_penguji_detail.berinilai',
                'unsur_nilai_penguji.bobot',
            )
            ->get();

        $pengujiJumlah = DB::table('penilaian_penguji')
            ->where('penilaian_penguji.ta_sidang_id', $taSidangId)
            ->count();

        $nilaiBobot = 0;
        foreach ($nilaiPengujiAll as $nilai) {
            $nilaiBobot += $nilai->berinilai * $nilai->bobot;
        }
        $rataRata = $nilaiBobot / $pengujiJumlah;

        DB::table('ta_sidang')
            ->where('ta_sidang_id', $taSidangId)
            ->update(
                [
                    'nilai_penguji' => $rataRata,
                ],
            );

        $taSidang = DB::table('ta_sidang')
            ->where('ta_sidang_id', $taSidangId)
            ->first();

        $nilaiPembimbing = $taSidang->nilai_pembimbing ?? 0;
        $nilaiPenguji = $taSidang->nilai_penguji ?? 0;

        $nilaiAkhir = ($nilaiPembimbing + $nilaiPenguji);

        DB::table('ta_sidang')
            ->where('ta_sidang_id', $taSidangId)
            ->update(
                [
                    'nilai_akhir' => $nilaiAkhir
                ],
            );

        return redirect()->route('ujian-sidang.index')->with('success', 'Nilai berhasil disimpan.');
    }
    public function showRevisi($ta_id)
    {
        $ta_mahasiswa = Ta::findOrFail($ta_id);
        $infoMhs = Ta::detailKelayakan($ta_id);
        return view('ujian-sidang.revisi', compact('ta_mahasiswa', 'infoMhs'));
    }

    public function showRevisi2($ta_id)
    {
        $ta_mahasiswa = Ta::findOrFail($ta_id);
        $infoMhs = Ta::detailKelayakan2($ta_id);

        return view('ujian-sidang.revisi2', compact('ta_mahasiswa', 'infoMhs'));
    }

    public function CetakSuratTugas($id)
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
        // dd($noSk);
        if (!$noSk) {
            return redirect()->route('ta.index')->with('error', 'Nomor SK tidak ditemukan.');
        }

        $pdf = new CustomPdf('P', 'mm', [210, 330]);

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
        $pdf->Cell(0, 10, 'Semarang, 23 Agustus 2023', 0, 1, 'R');

        $signaturePath = public_path('dist/img/ttd_kajur.png');
        $pdf->Image($signaturePath, $pdf->GetX() + 120, $pdf->GetY(), 70, 45);


        return response($pdf->Output('S'), 200)->header('Content-Type', 'application/pdf');
    }

    public function nilaiPembimbing($ta_sidang_id)
    {
        $info_sidang = DB::selectOne("SELECT
                                        tas.mhs_nim,
                                        mhs.mhs_nama,
                                        tas.tahun_akademik,
                                        DAYNAME(JS.tgl_sidang) hari,
                                        DATE_SUB(JS.tgl_sidang, INTERVAL 3 DAY) tgl_surat_tugas,
                                        JS.tgl_sidang,
                                        JS.sesi_id,
                                        JS.ruangan_id,
                                        RR.ruangan_nama,
                                        KPP.prodi_ID,
                                        DD.dosen_nama sekre_nama,
                                        DD.dosen_nip sekre_nip,
                                        DD.file_ttd sekre_ttd,
                                        SID.*
                                    FROM
                                        `ta_sidang` SID
                                    JOIN tas ON tas.ta_id = SID.ta_id
                                    JOIN mahasiswa mhs ON
                                        tas.mhs_nim = mhs.mhs_nim
                                    JOIN kode_prodi KPP ON
                                        mhs.prodi_ID = KPP.prodi_ID
                                    JOIN jadwal_sidang JS ON
                                        JS.jadwal_id = SID.jadwal_id
                                    JOIN ruangan_ta RR ON
                                        JS.ruangan_id = RR.ruangan_id
                                    JOIN dosen DD ON
                                        SID.dosen_nip = DD.dosen_nip
                                    WHERE
                                        SID.ta_sidang_id = $ta_sidang_id;");

        Carbon::setLocale('id');
        $info_sidang->tgl_sidang = Carbon::parse($info_sidang->tgl_sidang)->translatedFormat('j F Y');
        $info_sidang->tgl_surat_tugas = Carbon::parse($info_sidang->tgl_surat_tugas)->translatedFormat('j F Y');

        $bimbingans = DB::select("SELECT D.dosen_nama,D.file_ttd, B.* FROM bimbingans B JOIN dosen D ON B.dosen_nip = D.dosen_nip WHERE B.ta_id = " . $info_sidang->ta_id);

        $data_nilai = [];
        foreach ($bimbingans as $row) {
            $data_nilai[$row->dosen_nip] = $row;
        }

        $nilai_pembimbing = DB::select("SELECT
                                    D.dosen_nip,
                                    SUM((UPP.bobot * PP.berinilai)) jml_nilai,
                                    GROUP_CONCAT(CONCAT(UPP.unsur_nilai, ';;', UPP.bobot, ';;', PP.berinilai, ';;', (UPP.bobot * PP.berinilai)) SEPARATOR ';;;;') nilai
                                FROM
                                    `penilaian_pembimbing` PP
                                    JOIN dosen D 
                                        ON PP.dosen_nip = D.dosen_nip
                                    JOIN unsur_nilai_pembimbing UPP
                                        ON PP.nilai_id = UPP.nilai_id
                                WHERE
                                    PP.ta_sidang_id = $ta_sidang_id
                                GROUP BY D.dosen_nip
                                ;");

        foreach ($nilai_pembimbing as $row) {
            $temp_nilai = [];
            $aspek = explode(";;;;", $row->nilai);
            foreach ($aspek as $irow) {
                $aspek_detail = explode(";;", $irow);
                $temp_nilai[] = $aspek_detail;
            }
            $data_nilai[$row->dosen_nip]->nilai = $temp_nilai;
            $data_nilai[$row->dosen_nip]->jml_nilai = $row->jml_nilai;
        }

        $skData = Ta::getNoSKAndTahunAjaranProdiID($info_sidang->prodi_ID);
        $no_surat_tugas = $skData->no_sk;

        $jenis = ["1" => "Tugas Akhir", "2" => "Skripsi"];

        $view = view("ujian-sidang.nilai-pembimbing", [
            'jenis' => $jenis[$info_sidang->prodi_ID],
            'no_surat_tugas' => $no_surat_tugas,
            'info_sidang' => $info_sidang,
            'tgl_surat_tugas' => $info_sidang->tgl_surat_tugas,
            'data_nilai' => $data_nilai,
            'skData' => $skData
        ]);


        $mpdf = new Mpdf();
        $mpdf->WriteHTML($view);
        $mpdf->SetProtection(['copy', 'print']);
        $mpdf->showImageErrors = true;
        $mpdf->Output('Nilai Pembimbing ' . ucwords(strtolower($info_sidang->mhs_nama)) . '.pdf', 'I');
    }

    public function nilaiPenguji($ta_sidang_id)
    {
        $info_sidang = DB::selectOne("SELECT
                                        tas.mhs_nim,
                                        mhs.mhs_nama,
                                        tas.tahun_akademik,
                                        DAYNAME(JS.tgl_sidang) hari,
                                        DATE_SUB(JS.tgl_sidang, INTERVAL 3 DAY) tgl_surat_tugas,
                                        JS.tgl_sidang,
                                        JS.sesi_id,
                                        JS.ruangan_id,
                                        RR.ruangan_nama,
                                        KPP.prodi_ID,
                                        DD.dosen_nama sekre_nama,
                                        DD.dosen_nip sekre_nip,
                                        DD.file_ttd sekre_ttd,
                                        SID.*
                                    FROM
                                        `ta_sidang` SID
                                    JOIN tas ON tas.ta_id = SID.ta_id
                                    JOIN mahasiswa mhs ON
                                        tas.mhs_nim = mhs.mhs_nim
                                    JOIN kode_prodi KPP ON
                                        mhs.prodi_ID = KPP.prodi_ID
                                    JOIN jadwal_sidang JS ON
                                        JS.jadwal_id = SID.jadwal_id
                                    JOIN ruangan_ta RR ON
                                        JS.ruangan_id = RR.ruangan_id
                                    JOIN dosen DD ON
                                        SID.dosen_nip = DD.dosen_nip
                                    WHERE
                                        SID.ta_sidang_id = $ta_sidang_id;");

        $bimbingans = DB::select("SELECT 
                                    D.dosen_nama, 
                                    D.file_ttd, 
                                    B.* 
                                FROM 
                                    penilaian_penguji B 
                                    JOIN dosen D ON 
                                        B.dosen_nip = D.dosen_nip 
                                WHERE B.ta_sidang_id = " . $info_sidang->ta_sidang_id);

        Carbon::setLocale('id');
        $info_sidang->tgl_sidang = Carbon::parse($info_sidang->tgl_sidang)->translatedFormat('j F Y');
        $info_sidang->tgl_surat_tugas = Carbon::parse($info_sidang->tgl_surat_tugas)->translatedFormat('j F Y');

        $data_nilai = [];
        foreach ($bimbingans as $row) {
            $data_nilai[$row->dosen_nip] = $row;
        }

        $nilai_pembimbing = DB::select("SELECT
                                    D.dosen_nip,
                                    SUM((UPP.bobot * PP.berinilai)) jml_nilai,
                                    GROUP_CONCAT(CONCAT(UPP.unsur_nilai, ';;', UPP.bobot, ';;', PP.berinilai, ';;', (UPP.bobot * PP.berinilai)) SEPARATOR ';;;;') nilai
                                FROM
                                    penilaian_penguji_detail PP
                                    JOIN dosen D 
                                        ON PP.dosen_nip = D.dosen_nip
                                    JOIN unsur_nilai_penguji UPP
                                        ON PP.nilai_id = UPP.nilai_id
                                WHERE
                                    PP.ta_sidang_id = $ta_sidang_id
                                GROUP BY D.dosen_nip;");

        foreach ($nilai_pembimbing as $row) {
            $temp_nilai = [];
            $aspek = explode(";;;;", $row->nilai);
            foreach ($aspek as $irow) {
                $aspek_detail = explode(";;", $irow);
                $temp_nilai[] = $aspek_detail;
            }
            $data_nilai[$row->dosen_nip]->nilai = $temp_nilai;
            $data_nilai[$row->dosen_nip]->jml_nilai = $row->jml_nilai;
        }

        $skData = Ta::getNoSKAndTahunAjaranProdiID($info_sidang->prodi_ID);
        $no_surat_tugas = $skData->no_sk;

        $jenis = ["1" => "Tugas Akhir", "2" => "Skripsi"];

        $bimbingans = DB::select("SELECT D.dosen_nama,D.file_ttd, B.* FROM bimbingans B JOIN dosen D ON B.dosen_nip = D.dosen_nip WHERE B.ta_id = " . $info_sidang->ta_id);

        $view = view("ujian-sidang.nilai-penguji", [
            'jenis' => $jenis[$info_sidang->prodi_ID],
            'no_surat_tugas' => $no_surat_tugas,
            'info_sidang' => $info_sidang,
            'tgl_surat_tugas' => $info_sidang->tgl_surat_tugas,
            'data_nilai' => $data_nilai,
            'skData' => $skData,
            'bimbingans' => $bimbingans
        ]);

        $mpdf = new Mpdf();
        $mpdf->WriteHTML($view);
        $mpdf->SetProtection(['copy', 'print']);
        $mpdf->showImageErrors = true;
        $mpdf->Output('Nilai Penguji ' . ucwords(strtolower($info_sidang->mhs_nama)) . '.pdf', 'I');
    }

    public function beritaAcara($ta_sidang_id)
    {
        $info_sidang = DB::selectOne("SELECT
                                        tas.mhs_nim,
                                        mhs.mhs_nama,
                                        tas.tahun_akademik,
                                        DAYNAME(JS.tgl_sidang) hari,
                                        DATE_SUB(JS.tgl_sidang, INTERVAL 3 DAY) tgl_surat_tugas,
                                        JS.tgl_sidang,
                                        JS.sesi_id,
                                        JS.ruangan_id,
                                        RR.ruangan_nama,
                                        KPP.prodi_ID,
                                        DD.dosen_nama sekre_nama,
                                        DD.dosen_nip sekre_nip,
                                        DD.file_ttd sekre_ttd,
                                        SID.*
                                    FROM
                                        `ta_sidang` SID
                                    JOIN tas ON tas.ta_id = SID.ta_id
                                    JOIN mahasiswa mhs ON
                                        tas.mhs_nim = mhs.mhs_nim
                                    JOIN kode_prodi KPP ON
                                        mhs.prodi_ID = KPP.prodi_ID
                                    JOIN jadwal_sidang JS ON
                                        JS.jadwal_id = SID.jadwal_id
                                    JOIN ruangan_ta RR ON
                                        JS.ruangan_id = RR.ruangan_id
                                    JOIN dosen DD ON
                                        SID.dosen_nip = DD.dosen_nip
                                    WHERE
                                        SID.ta_sidang_id = $ta_sidang_id;");

        Carbon::setLocale('id');
        $info_sidang->tgl_sidang = Carbon::parse($info_sidang->tgl_sidang)->translatedFormat('j F Y');
        $info_sidang->tgl_surat_tugas = Carbon::parse($info_sidang->tgl_surat_tugas)->translatedFormat('j F Y');

        $bimbingans = DB::select("SELECT 
                                        D.dosen_nama,
                                        D.dosen_nama_singkatan, 
                                        D.file_ttd, 
                                        B.* 
                                    FROM bimbingans B 
                                        JOIN dosen D 
                                            ON B.dosen_nip = D.dosen_nip 
                                    WHERE B.ta_id = " . $info_sidang->ta_id);

        $data_nilai = [];
        foreach ($bimbingans as $row) {
            $data_nilai[$row->dosen_nip] = $row;
        }

        $nilai_pembimbing = DB::select("SELECT
                                    D.dosen_nip,
                                    SUM((UPP.bobot * PP.berinilai)) jml_nilai,
                                    GROUP_CONCAT(CONCAT(UPP.unsur_nilai, ';;', UPP.bobot, ';;', PP.berinilai, ';;', (UPP.bobot * PP.berinilai)) SEPARATOR ';;;;') nilai
                                FROM
                                    `penilaian_pembimbing` PP
                                    JOIN dosen D 
                                        ON PP.dosen_nip = D.dosen_nip
                                    JOIN unsur_nilai_pembimbing UPP
                                        ON PP.nilai_id = UPP.nilai_id
                                WHERE
                                    PP.ta_sidang_id = $ta_sidang_id
                                GROUP BY D.dosen_nip
                                ;");

        foreach ($nilai_pembimbing as $row) {
            $temp_nilai = [];
            $aspek = explode(";;;;", $row->nilai);
            foreach ($aspek as $irow) {
                $aspek_detail = explode(";;", $irow);
                $temp_nilai[] = $aspek_detail;
            }
            $data_nilai[$row->dosen_nip]->nilai = $temp_nilai;
            $data_nilai[$row->dosen_nip]->jml_nilai = $row->jml_nilai;
        }

        $skData = Ta::getNoSKAndTahunAjaranProdiID($info_sidang->prodi_ID);
        $no_surat_tugas = $skData->no_sk;

        $jenis = ["1" => "Tugas Akhir", "2" => "Skripsi"];


        //rekap ujian
        $pengujis = DB::select("SELECT 
                                    D.dosen_nama, 
                                    D.dosen_nama_singkatan, 
                                    D.file_ttd, 
                                    B.* 
                                FROM 
                                    penilaian_penguji B 
                                    JOIN dosen D ON 
                                        B.dosen_nip = D.dosen_nip 
                                WHERE B.ta_sidang_id = " . $info_sidang->ta_sidang_id);

        $data_nilai_penguji = [];
        foreach ($pengujis as $row) {
            $data_nilai_penguji[$row->dosen_nip] = $row;
        }

        $nilai_penguji = DB::select("SELECT
                                    D.dosen_nip,
                                    SUM((UPP.bobot * PP.berinilai)) jml_nilai,
                                    GROUP_CONCAT(CONCAT(UPP.unsur_nilai, ';;', UPP.bobot, ';;', PP.berinilai, ';;', (UPP.bobot * PP.berinilai)) SEPARATOR ';;;;') nilai
                                FROM
                                    penilaian_penguji_detail PP
                                    JOIN dosen D 
                                        ON PP.dosen_nip = D.dosen_nip
                                    JOIN unsur_nilai_penguji UPP
                                        ON PP.nilai_id = UPP.nilai_id
                                WHERE
                                    PP.ta_sidang_id = $ta_sidang_id
                                GROUP BY D.dosen_nip;");

        foreach ($nilai_penguji as $row) {
            $temp_nilai = [];
            $aspek = explode(";;;;", $row->nilai);
            foreach ($aspek as $irow) {
                $aspek_detail = explode(";;", $irow);
                $temp_nilai[] = $aspek_detail;
            }
            $data_nilai_penguji[$row->dosen_nip]->nilai = $temp_nilai;
            $data_nilai_penguji[$row->dosen_nip]->jml_nilai = $row->jml_nilai;
        }

        $skData = Ta::getNoSKAndTahunAjaranProdiID($info_sidang->prodi_ID);
        $no_surat_tugas = $skData->no_sk;

        $params = [
            'jenis' => $jenis[$info_sidang->prodi_ID],
            'no_surat_tugas' => $no_surat_tugas,
            'info_sidang' => $info_sidang,
            'tgl_surat_tugas' => $info_sidang->tgl_surat_tugas,
            'data_nilai' => $data_nilai,
            'data_nilai_penguji' => $data_nilai_penguji,
            'skData' => $skData
        ];

        $view = view("ujian-sidang.nilai-akhir", $params);

        $mpdf = new Mpdf();
        $mpdf->WriteHTML($view);
        $mpdf->SetProtection(['copy', 'print']);
        $mpdf->showImageErrors = true;
        $mpdf->Output('Nilai Penguji ' . ucwords(strtolower($info_sidang->mhs_nama)) . '.pdf', 'I');
    }

    public function CetakRekapNilai($id)
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
            $kataujian = "Skripsi   :";
        } elseif ($belakang_jenjang == '34') {
            $prod = "IK";
            $kelasnya = "3";
            $kataujian = "TA        :";
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
        $ta_sidang_id = $infoujian->ta_sidang_id;

        $nilaiPenguji1 = Ta::getNilaiPenguji($ta_sidang_id, $infoujian->penguji1_nip);
        $nilaiPenguji2 = Ta::getNilaiPenguji($ta_sidang_id, $infoujian->penguji2_nip);
        $nilaiPenguji3 = Ta::getNilaiPenguji($ta_sidang_id, $infoujian->penguji3_nip);
        $nilai_rata = number_format((($nilaiPenguji1 + $nilaiPenguji2 + $nilaiPenguji3) / 3), 2);

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

        //tanggal 3 hari
        $datetime_sidang = new \DateTime($tanggal_sidang);
        $datetime_3hari = $datetime_sidang->sub(new \DateInterval('P3D'));
        $tanggal_3hari = $datetime_3hari->format('d-m-Y');
        $nama_hari_inggris2 = date('l', strtotime($tanggal_3hari));
        $hari_indonesia2 = $hari[array_search($nama_hari_inggris2, array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'))];
        $tanggal_dmy2 = date('d-m-Y', strtotime($tanggal_3hari));
        list($tanggal2, $bulan2, $tahun2) = explode('-', $tanggal_dmy2);
        $bulan_indonesia2 = array(
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        );
        $nama_bulan2 = $bulan_indonesia2[$bulan2];
        $tanggal_indonesia2 = $tanggal2 . ' ' . $nama_bulan2 . ' ' . $tahun2;

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

        $pdf = new CustomPdfRekapNilai('P', 'mm', 'A4');

        $pdf->AddPage();

        $imagePath = public_path('dist/img/header_rekapitulasi_nilai.png');

        $pdf->Image($imagePath, 10, 10, 190);

        $pdf->SetFont('Arial', '', 12);

        $pdf->Ln(45);

        // Set width of columns to 190 for single column
        $pdf->SetWidths([190]);

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

        // Tentukan posisi awal untuk border
        $xStart = 10 - 1; // X-coordinate of the start of the border
        $yStart = $pdf->GetY(); // Y-coordinate of the start of the border

        // Mengambil data nomor SK dan tahun ajaran
        $skData = Ta::getNoSKAndTahunAjaran($kode_prodi);
        $nomorSK = $skData->no_sk;

        // Konten utama
        $pdf->SetFont('Arial', '', 12);
        $content = "Berdasarkan surat tugas $nomorSK tanggal $tanggal_indonesia2 Tim Penguji skripsi\n";
        $content .= "telah melaksanakan ujian skripsi mahasiswa:\n";
        $content .= "Nama             : {$detailta->mhs_nama}\n";
        $content .= "NIM/Kelas      : {$detailta->mhs_nim}/{$prod}-{$kelasnya}{$kelas}\n";
        $content .= "Judul " . $kataujian . " {$infoujian->judul_final}\n";
        $content .= "Ruang Ujian   : {$infoujian->ruangan_nama}\n";
        $content .= "dengan hasil berikut:\n";

        // Cetak konten utama dalam MultiCell untuk menjaga format
        $pdf->MultiCell(0, 10, $content, 0, 'L');
        $pdf->Ln(5); // Tambahkan jarak setelah konten

        // Membuat tabel
        $pdf->SetFont('Arial', 'B', 12);
        $header = [" No.", " Nama Penguji", " Jabatan", "   Nilai", " Tanda tangan"];
        $pdf->SetWidths([10, 90, 30, 20, 40]); // Set lebar kolom

        // Cetak header tabel
        $pdf->Row($header);
        $pdf->SetFont('Arial', '', 12);
        // Menambahkan 3 baris data dengan tinggi spesifik dan menjaga teks di tengah
        $rowHeight = 15; // Set tinggi baris

        $pdf->Row([" 1", " " . $infoujian->penguji1_nama, " Penguji I", "    {$nilaiPenguji1}", $infoujian->penguji1_ttd_path], $rowHeight);
        $pdf->Row([" 2", " " . $infoujian->penguji2_nama, " Penguji II", "    {$nilaiPenguji2}", $infoujian->penguji2_ttd_path], $rowHeight);
        $pdf->Row([" 3", " " . $infoujian->penguji3_nama, " Penguji III", "    {$nilaiPenguji3}", $infoujian->penguji3_ttd_path], $rowHeight);
        $pdf->Cell(135, 7, "Nilai rata-rata ujian skripsi: (maks. 50)", 0, 0);
        $pdf->Cell(0, 7, $nilai_rata, 0, 1);
        $pdf->Cell(0, 7, "Rangkuman nilai ini dapat dipergunakan sebagaimana mestinya.", 0, 'L');

        // Ambil file tanda tangan ketua tim penguji dan sekretaris
        $fileTtdKetua = public_path('dist/img/' . $detailta->dosen[0]["file_ttd"]);
        $fileTtdSekretaris = public_path('dist/img/' . $infoujian->sekretaris_ttd_path);

        //ambil tanggal sekarang
        $tanggal_sekarang = date('d-m-Y', strtotime(date("Y-m-d")));
        list($tanggal2, $bulan2, $tahun2) = explode('-', $tanggal_sekarang);
        $nama_bulan2 = $bulan_indonesia[$bulan2];
        $tanggal_now = $tanggal2 . ' ' . $nama_bulan2 . ' ' . $tahun2;
        $pdf->Ln(10);
        $pdf->Cell(110, 5, '', 0, 0);
        $pdf->Cell(0, 5, 'Semarang, ' . $tanggal_indonesia, 0, 1);
        $pdf->Cell(120, 5, '', 0, 1);
        $pdf->Cell(50, 5, 'Ketua Tim Penguji,', 0, 0);
        $pdf->Cell(60, 5, '', 0, 0);
        $pdf->Cell(50, 5, 'Sekretaris Tim Penguji,', 0, 1);

        // Tambahkan gambar tanda tangan ketua tim penguji
        if (file_exists($fileTtdKetua)) {
            $pdf->Image($fileTtdKetua, $pdf->GetX() + 10, $pdf->GetY(), 17);
        }

        // Tambahkan gambar tanda tangan sekretaris
        $pdf->Cell(50, 5, '', 0, 0); // Pindahkan posisi ke kolom sekretaris
        if (file_exists($fileTtdSekretaris)) {
            $pdf->Image($fileTtdSekretaris, $pdf->GetX() + 70, $pdf->GetY(), 17);
        }
        $pdf->Cell(50, 20, '', 0, 1); // Pindahkan ke baris berikutnya

        $pdf->SetFont('Arial', 'U', 12);
        $pdf->Cell(50, 5, $detailta->dosen[0]["dosen_nama"], 0, 0);
        $pdf->Cell(60, 5, '', 0, 0);
        $pdf->Cell(50, 5, $infoujian->sekretaris_nama, 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 5, "NIP. " . $detailta->dosen[0]["dosen_nip"], 0, 0);
        $pdf->Cell(60, 5, '', 0, 0);
        $pdf->Cell(50, 5, "NIP. " . $infoujian->sekretaris_nip, 0, 1);

        // Tentukan posisi akhir untuk border
        $yEnd = $pdf->GetY(); // Y-coordinate of the end of the border

        // Tambahkan border
        $pdf->Rect($xStart, $yStart, 190 + 2, ($yEnd - $yStart) + 5);

        return response($pdf->Output('S'), 200)->header('Content-Type', 'application/pdf');
    }
}
