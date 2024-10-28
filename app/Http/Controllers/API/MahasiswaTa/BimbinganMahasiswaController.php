<?php

namespace App\Http\Controllers\API\MahasiswaTa;

use App\Http\Controllers\Controller;
use App\Models\Bimbingan;
use App\Models\BimbinganLog;
use App\Models\KodeProdi;
use App\Models\mahasiswa;
use App\Models\Ta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;
use Mpdf;
use Mpdf\Mpdf as MpdfMpdf;

// Mahasiswa
class BimbinganMahasiswaController extends Controller
{
    public function index(Request $request)
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

            $logCollect = collect(BimbinganLog::BimbinganLog($id));
            $logCollectJumlah = collect(BimbinganLog::BimbinganLog($id));

            $masterJumlah = DB::table('bimbingan_counts')->value('bimbingan_counts.total_bimbingan');

            $mahasiswa = Bimbingan::Mahasiswa($id);

            if ($request->filled('pembimbing')) {
                $logCollect = $logCollect->where('dosen_nip', $request->input('pembimbing'));
            }
            if ($request->filled('verifikasi')) {
                $logCollect = $logCollect->where('bimb_status', $request->input('verifikasi'));
            }

            Carbon::setLocale('id');
            foreach ($logCollect as $item) {
                //$item->bimb_judul = Str::limit($item->bimb_judul, 15, '...');
                //$item->bimb_file_original = Str::limit($item->bimb_file_original, 20, '...');

                $item->format_tanggal = Carbon::parse($item->bimb_tgl)->translatedFormat('l, j F Y');
            }
            
            return response()->json([
                'success' => true,
                'logCollect' => $logCollect,
                'data' => $mahasiswa,
                'masterJumlah' => $masterJumlah,
                'logCollectJumlah' => $logCollectJumlah
            ]);
        }
    }

    public function create()
    {
        $id = Auth::user()->id;
        $mahasiswa = Bimbingan::Mahasiswa($id);
        
        return response()->json([
            'success' => true,
            'data' => $mahasiswa
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required',
            'desk' => 'required',
            'tgl' => 'required',
            'pembimbing' => 'required',
            'draft' => 'nullable|mimetypes:application/pdf|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Bimbingan gagal ditambah. Periksa kembali data anda.',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            if ($request->hasFile('draft')) {
                $draft = $request->file('draft');
                $nama_file = date('Ymdhis') . '.' . $draft->getClientOriginalExtension();
                $draft->storeAs('public/draft_ta', $nama_file);
                $nama_file_original = $draft->getClientOriginalName();
            } else {
                $nama_file = null;
                $nama_file_original = null;
            }

            BimbinganLog::insert([
                'bimbingan_id' => $request->pembimbing,
                'bimb_tgl' => $request->tgl,
                'bimb_judul' => $request->judul,
                'bimb_desc' => $request->desk,
                'bimb_file_original' => $nama_file_original,
                'bimb_file' => $nama_file,
                'bimb_status' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bimbingan berhasil disimpan'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat masalah di server: ' . $th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $bimbLog = BimbinganLog::findOrFail($id);
            $idUser = Auth::user()->id;
            $mahasiswa = Bimbingan::Mahasiswa($idUser);

            return response()->json([
                'success' => true,
                'data' => [
                    'mahasiswa' => $mahasiswa,
                    'bimbLog' => $bimbLog
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Log tidak ditemukan'
            ], 404);
        }
    }

    public function edit($id)
    {
        try {
            $bimbLog = BimbinganLog::findOrFail($id);
            $idUser = Auth::user()->id;
            $mahasiswa = Bimbingan::Mahasiswa($idUser);
            $batas = BimbinganLog::batasEdit($id);

            if ($bimbLog->bimb_status == 0 && $mahasiswa->ta_id == $batas->ta_id) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'mahasiswa' => $mahasiswa,
                        'bimbLog' => $bimbLog,
                        'batas' => $batas
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak memiliki akses'
                ], 403);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Log tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required',
            'desk' => 'required',
            'tgl' => 'required',
            'pembimbing' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Bimbingan gagal diupdate. Periksa kembali data anda.',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $bimbLog = BimbinganLog::findOrFail($id);
            $bimbLog->bimbingan_id = $request->post('pembimbing');
            $bimbLog->bimb_judul = $request->post('judul');
            $bimbLog->bimb_desc = $request->post('desk');
            $bimbLog->bimb_tgl = $request->post('tgl');

            if ($request->hasFile('draft')) {
                $fileLama = public_path('storage/draft_ta/' . $bimbLog->bimb_file);
                if (file_exists($fileLama) && !empty($bimbLog->bimb_file)) {
                    unlink($fileLama);
                }

                $draft = $request->file('draft');
                $nama_file = date('Ymdhis') . '.' . $draft->getClientOriginalExtension();
                $draft->storeAs('public/draft_ta', $nama_file);
                $bimbLog->bimb_file = $nama_file;
                $bimbLog->bimb_file_original = $draft->getClientOriginalName();
            }

            $bimbLog->update();

            return response()->json([
                'success' => true,
                'message' => 'Bimbingan berhasil diupdate'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat masalah di server: ' . $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $bimbLog = BimbinganLog::findOrFail($id);

            $fileLama = public_path('storage/draft_ta/' . $bimbLog->bimb_file);
            if (file_exists($fileLama)) {
                unlink($fileLama);
            }

            $bimbLog->delete();

            return response()->json([
                'success' => true,
                'message' => 'Log berhasil dihapus'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat masalah di server: ' . $th->getMessage()
            ], 500);
        }
    }


    public function cetak_persetujuan_sidang()
    {
        $mhs = mahasiswa::where("email", Auth::user()->email)->first();
        $ta_mhs = collect(DB::select("SELECT mhs_nim FROM tas_mahasiswa WHERE ta_id IN (SELECT ta_id FROM tas_mahasiswa WHERE mhs_nim = '" . $mhs->mhs_nim . "') ORDER BY ta_id ASC"));
        $prodi_id = $mhs->prodi_ID;
        $ta = DB::selectOne("SELECT * FROM tas WHERE mhs_nim = '" . $mhs->mhs_nim . "'");
        $prodi = KodeProdi::where("prodi_ID", $prodi_id)->first();

        $temp = [];
        foreach ($ta_mhs->pluck('mhs_nim') as $row) {
            $temp[] = $row;
        }
        $mahasiswa = DB::select("SELECT * FROM mahasiswa WHERE mhs_nim IN (" . implode(",", $temp) . ")");

        $pembimbings = collect(DB::select("SELECT D.file_ttd, D.dosen_nama, B.* FROM bimbingans B JOIN dosen D ON B.dosen_nip = D.dosen_nip WHERE ta_id = " . $ta->ta_id));

        $pembimbing = [];
        $bimb_id = [];
        $approve = "";
        foreach ($pembimbings as $row) {
            $bimb_id[] = $row->bimbingan_id;
            $pembimbing[] = [
                "nama" => $row->dosen_nama,
                "nip" => $row->dosen_nip,
                "ttd" => $row->file_ttd
            ];
        }

        $tgl = DB::selectOne("SELECT MAX(bimb_tgl) tgl FROM bimbingan_log WHERE bimbingan_id IN (" . implode(",", $bimb_id) . ")");

        $jenis = ["1" => "Tugas Akhir", "2" => "Skripsi"];

        Carbon::setLocale('id');

        $view = view("cetak-cetak.persetujuan-sidang", [
            "jenis" => $jenis,
            "prodi_id" => $prodi_id,
            "prodi_nama" => $prodi->program_studi,
            "mahasiswa" => $mahasiswa,
            "judul_ta" => $ta->ta_judul,
            "tanggal_approve" => Carbon::parse($tgl->tgl)->translatedFormat('j F Y'),
            "pembimbing" => $pembimbing
        ]);
        $mpdf = new MpdfMpdf();
        $mpdf->WriteHTML($view);
        $mpdf->SetProtection(['copy', 'print']);
        $mpdf->showImageErrors = true;
        $mpdf->Output('Persetujuan Sidang ' . ucwords(strtolower($mhs->mhs_nama)) . '.pdf', 'I');

        //echo $view;
    }

    // BimbinganMahasiswaController.php
    public function CetakLembarKontrol($id, $sebagai)
    {
        $bimbinganData = Bimbingan::getBimbinganData($id, $sebagai);

        $nim = $bimbinganData[0]->mhs_nim;
        $nama = $bimbinganData[0]->mhs_nama;
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
        $depan_kelas = substr($nim, 5, 1);
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
        $susun_kelas = $prod . "-" . $kelasnya . $kelas;

        $pdf = new CustomPdfMahasiswa('P', 'mm', 'A4');
        $pdf->AddPage();

        // Path gambar header
        $imagePath = public_path('dist/img/header_lembar_kontrol.png');
        $pdf->Image($imagePath, 10, 10, 190);
        $pdf->Ln(45); // Jarak setelah gambar

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetWidths([90, 100]);

        $data = [
            "Nama : " . $nama . "\n\nKelas : " . $susun_kelas . "\n\nNIM : " . $nim . "\n\n",
            "Judul Tugas Akhir/Skripsi : " . $bimbinganData[0]->ta_judul
        ];

        $underline = [0, 1, 0];
        $pdf->Row($data, $underline);

        // Add the second table
        $pdf->SetWidths([10, 30, 110, 40]); // Adjust widths as needed
        $pdf->Row(["No.", "Tanggal", "Uraian", "Tandatangan Pembimbing"], [0, 0, 0, 0]);

        // Loop through your bimbingan data
        foreach ($bimbinganData as $index => $bimbingan) {
            $rowData = [
                $index + 1,
                Carbon::parse($bimbingan->bimb_tgl)->format('d-m-Y'),
                $bimbingan->bimb_judul . "\n" . $bimbingan->bimb_desc,
                '' // This will be replaced with the image
            ];

            // Get the signature image path
            $signatureImagePath = public_path('dist/img/' . $bimbingan->file_ttd);
            $images = [null, null, null, $signatureImagePath];

            $pdf->Row($rowData, [0, 0, 0, 0], $images);
        }

        // Add some space after the table
        $pdf->Ln(10); // Adjust as needed

        // Add the footer section
        $this->addFooterSection($pdf, $bimbinganData, $sebagai);

        return response($pdf->Output('S'), 200)->header('Content-Type', 'application/pdf');
    }

    function addFooterSection($pdf, $bimbinganData, $sebagai)
    {
        $currentY = $pdf->GetY();
        $pageHeight = $pdf->GetPageHeight();
        $bottomMargin = 20;

        $footerHeight = 5 + 5 + 30 + 5 + 5;

        if (($pageHeight - $bottomMargin - $currentY) < $footerHeight) {
            $pdf->AddPage();
        }

        $pdf->SetX(120);
        $pdf->Cell(0, 5, "Semarang, " . Carbon::now()->format('d-m-Y'), 0, 1, 'L');
        $pdf->SetX(120);
        $pdf->Cell(0, 5, "Pembimbing $sebagai", 0, 1, 'L');
        // $pdf->Ln(30);

        $dosenNip = $bimbinganData[0]->dosen_nip;
        $dosenName = Bimbingan::getDosenName($dosenNip);

        // Get the signature image path for the lecturer
        $signatureImagePath = public_path('dist/img/' . $bimbinganData[0]->file_ttd); // Ganti ke file_ttd
        if (file_exists($signatureImagePath)) {
            // Menentukan ukuran gambar
            list($originalWidth, $originalHeight) = getimagesize($signatureImagePath);
            $maxWidth = 50; // Lebar maksimum gambar
            $maxHeight = 20; // Tinggi maksimum gambar

            // Menghitung rasio aspek
            $aspectRatio = $originalWidth / $originalHeight;

            // Menghitung dimensi baru
            if ($maxWidth / $aspectRatio <= $maxHeight) {
                $newWidth = $maxWidth;
                $newHeight = $maxWidth / $aspectRatio;
            } else {
                $newHeight = $maxHeight;
                $newWidth = $maxHeight * $aspectRatio;
            }

            // Menambahkan gambar tanda tangan dosen
            $pdf->Image($signatureImagePath, 130, $pdf->GetY(), $newWidth, $newHeight);
            $pdf->Ln(5); // Jarak setelah gambar
        }
        $pdf->Ln(20);

        $pdf->SetX(120);
        $nameHeight = 5;
        if (($pageHeight - $bottomMargin - $pdf->GetY()) < $nameHeight) {
            $pdf->AddPage();
        }
        $pdf->Cell(0, $nameHeight, $dosenName, 0, 1, 'L');

        $textWidth = $pdf->GetStringWidth($dosenName);
        $currentLineY = $pdf->GetY() - $nameHeight + 5;
        $pdf->Line(120, $currentLineY, 120 + $textWidth, $currentLineY);

        $pdf->SetXY(120, $currentLineY + 1);
        $nipHeight = 5;
        if (($pageHeight - $bottomMargin - $pdf->GetY()) < $nipHeight) {
            $pdf->AddPage();
        }
        $pdf->Cell(0, $nipHeight, "NIP. " . $dosenNip, 0, 1, 'L');
    }
}
