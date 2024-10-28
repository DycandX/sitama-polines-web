<?php

namespace App\Http\Controllers\API;

use App\Mail\ResetPasswordMail;
use App\Models\mahasiswa;
use App\Models\Ruangan;
use App\Models\User;
use Exception;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only('index');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $jadwal = DB::select("SELECT
                                    JSS.*,
                                    SESI.sesi_nama,
                                    SESI.sesi_waktu_mulai,
                                    SESI.sesi_waktu_selesai,
                                    SESI.sesi_waktu_mulai_jumat,
                                    SESI.sesi_waktu_selesai_jumat,
                                    RUANG.ruangan_nama,
                                    TSS.judul_final,
                                    GROUP_CONCAT(
                                        DISTINCT(CONCAT(MHS.mhs_nama, ' (', MHS.mhs_nim, ')')) SEPARATOR ';;'
                                    ) mhs,
                                    SEKRE.dosen_nip sekre_nip,
                                    SEKRE.dosen_nama sekre_nama,
                                    GROUP_CONCAT(DISTINCT(PENGUJI.dosen_nama) ORDER BY NPENGUJI.urutan SEPARATOR ';;') penguji,
                                    GROUP_CONCAT(DISTINCT(PEMBIMBING.dosen_nama) ORDER BY NPEMBIMBING.urutan SEPARATOR ';;') pembimbing
                                FROM
                                    jadwal_sidang JSS
                                JOIN sesi_ta SESI ON
                                    JSS.sesi_id = SESI.sesi_id
                                JOIN ruangan_ta RUANG ON
                                    JSS.ruangan_id = RUANG.ruangan_id
                                LEFT JOIN ta_sidang TSS ON
                                    JSS.jadwal_id = TSS.jadwal_id
                                LEFT JOIN dosen SEKRE ON
                                    TSS.dosen_nip = SEKRE.dosen_nip
                                LEFT JOIN penilaian_penguji NPENGUJI ON
                                    TSS.ta_sidang_id = NPENGUJI.ta_sidang_id
                                LEFT JOIN dosen PENGUJI ON
                                    NPENGUJI.dosen_nip = PENGUJI.dosen_nip
                                LEFT JOIN tas TAA ON
                                    TSS.ta_id = TAA.ta_id
                                LEFT JOIN bimbingans NPEMBIMBING ON
                                    TAA.ta_id = NPEMBIMBING.ta_id
                                LEFT JOIN dosen PEMBIMBING ON 
                                    NPEMBIMBING.dosen_nip = PEMBIMBING.dosen_nip
                                LEFT JOIN mahasiswa MHS ON
                                    TAA.mhs_nim = MHS.mhs_nim
                                GROUP BY
                                    JSS.jadwal_id,
                                    TSS.judul_final,
                                    SEKRE.dosen_nip");

        $now = Carbon::now(); // Untuk mendapatkan tanggal dan waktu saat ini

        $jadwal_no_mhs_future = [];
        $jadwal_with_mhs_future = [];
        $jadwal_past = [];

        foreach ($jadwal as $row) {
            $row_data = [
                $row->judul_final,
                implode("<br>", explode(";;", $row->mhs)),
                implode("<br>", explode(";;", $row->pembimbing)),
                implode("<br>", explode(";;", $row->penguji)),
                $row->sekre_nama,
            ];

            // Cek apakah tgl_sidang sudah lewat atau belum
            if (strtotime($row->tgl_sidang) >= strtotime($now)) {
                // Tanggal belum lewat
                if (empty($row->mhs)) {
                    // Tidak ada mahasiswa
                    $jadwal_no_mhs_future[] = [
                        'tgl_sidang' => $row->tgl_sidang,
                        'sesi_nama' => $row->sesi_nama,
                        'ruangan_nama' => $row->ruangan_nama,
                        'data' => $row_data
                    ];
                } else {
                    // Ada mahasiswa
                    $jadwal_with_mhs_future[] = [
                        'tgl_sidang' => $row->tgl_sidang,
                        'sesi_nama' => $row->sesi_nama,
                        'ruangan_nama' => $row->ruangan_nama,
                        'data' => $row_data
                    ];
                }
            } else {
                // Tanggal sudah lewat
                $jadwal_past[] = [
                    'tgl_sidang' => $row->tgl_sidang,
                    'sesi_nama' => $row->sesi_nama,
                    'ruangan_nama' => $row->ruangan_nama,
                    'data' => $row_data
                ];
            }
        }

        // Urutkan array berdasarkan tgl_sidang dari yang terbaru (untuk jadwal di masa depan)
        usort($jadwal_no_mhs_future, function($a, $b) {
            return strtotime($b['tgl_sidang']) - strtotime($a['tgl_sidang']);
        });

        usort($jadwal_with_mhs_future, function($a, $b) {
            return strtotime($b['tgl_sidang']) - strtotime($a['tgl_sidang']);
        });

        // Urutkan array berdasarkan tgl_sidang dari yang terlama (untuk jadwal yang sudah lewat)
        usort($jadwal_past, function($a, $b) {
            return strtotime($b['tgl_sidang']) - strtotime($a['tgl_sidang']);
        });

        // Gabungkan jadwal: 
        // 1. Jadwal tanpa mahasiswa dan tanggal di masa depan
        // 2. Jadwal dengan mahasiswa dan tanggal di masa depan
        // 3. Jadwal di masa lalu
        $merged_jadwal = array_merge($jadwal_no_mhs_future, $jadwal_with_mhs_future, $jadwal_past);

        // Susun data ke dalam array $temp sesuai tgl_sidang, sesi_nama, dan ruangan_nama
        $temp = [];
        foreach ($merged_jadwal as $row) {
            $temp[$row['tgl_sidang']][$row['sesi_nama']][$row['ruangan_nama']] = $row['data'];
        }

        $ruang = Ruangan::get();

        return response()->json([
            'success' => true,
            'jadwal' => $temp,
            'ruangan' => $ruang
        ]);
    }


    public function streamDocument($enc_path)
    {
        $dl       = request()->get('dl');
        $filename = request()->get('filename'); //FILENAME WAJIB SUDAH ADA EKSTENSINYA

        $file     = decrypt($enc_path);
        $mimetype = mime_content_type($file);

        $parts = explode(".", $file);
        $ext   = $parts[count($parts) - 1];

        header('Content-Type: ' . $mimetype);
        header('Content-Description: File Transfer');
        if ($dl == '1') {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        } else {
            header('Content-Disposition: inline; filename="' . $filename . '"');
        }
        readfile($file);
        die;
    }

    public function resetPasswordFormRequest()
    {
        return view('reset');
    }
    public function resetPasswordFormRequestAction()
    {
        $nim =  request()->post('nim');

        $mhs = mahasiswa::where('mhs_nim', $nim)->first();

        if ($mhs) {
            $user = User::where('email', $mhs->email)->first();
            if ($user) {
                Mail::to($user->email)
                    ->send(new ResetPasswordMail($user));

                return view('reset-done', ["email" => $this->sensorEmail($user->email)]);
            } else {
                return redirect()->route('reset-password-form')->with('error', 'User belum dibuat. Mohon menghubungi Kaprodi.');
            }
        } else {
            return redirect()->route('reset-password-form')->with('error', 'Data mahasiswa tidak ditemukan. Mohon menghubungi Kaprodi.');
        }
    }

    public function resetPassword()
    {
        $token = request()->get("token");

        $user_reset = DB::table("users_reset_password")->where("reset_password_token", "=", $token)->first();

        if ($user_reset) {
            $user = User::where("id", $user_reset->user_id)->first();
            if ($user) {
                return view('reset-password', ["user" => $user]);
            } else {
                DB::table("log_suspicious_action")->insert([
                    "message" => "Tidka ada user dg token tersebut. Token: " . $token . " user_id: " . $user_reset->user_id,
                    "url" => request()->getUri(),
                    "created_at" => date("Y-m-d H:i:s")
                ]);

                return view('reset-invalid-user');
            }
        } else {
            DB::table("log_suspicious_action")->insert([
                "message" => "Invalid reset password token. Token: " . $token,
                "url" => request()->getUri(),
                "created_at" => date("Y-m-d H:i:s")
            ]);
            return view('reset-invalid-token');
        }
    }
    public function resetPasswordAction()
    {
        try {
            $user_id = decrypt(request()->post("user"));

            if (request()->post('password') == request()->post('confirmPassword')) {
                DB::table("users")->where("id", "=", $user_id)->update(["password" => Hash::make(request()->post('confirmPassword'))]);
                DB::table("users_reset_password")->where("user_id", "=", $user_id)->delete();
                return view("reset-password-done");
            } else {
                return redirect()->back()->with("error", "Password dan Konfirmasi tidak sama.");
            }
        } catch (Exception $e) {
            DB::table("log_suspicious_action")->insert([
                "message" => "Problem reset password. User: " . request()->post("user"),
                "url" => request()->getUri(),
                "created_at" => date("Y-m-d H:i:s")
            ]);
            return view("errors.500");
        }
    }

    function sensorEmail($email, $numFront = 3, $numBack = 3)
    {
        // Pisahkan email menjadi bagian username dan domain
        list($username, $domain) = explode('@', $email);

        // Hitung panjang username
        $usernameLength = strlen($username);

        // Pastikan jumlah huruf yang ingin ditampilkan tidak lebih dari panjang username
        if ($numFront + $numBack > $usernameLength) {
            $numFront = $usernameLength;
            $numBack = 0;
        }

        // Ambil bagian depan dan belakang dari username
        $front = substr($username, 0, $numFront);
        $back = substr($username, -$numBack);

        // Buat bagian tengah yang disensor
        $middle = str_repeat('*', $usernameLength - $numFront - $numBack);

        // Gabungkan bagian depan, tengah, dan belakang
        $censoredUsername = $front . $middle . $back;

        // Gabungkan username yang sudah disensor dengan domain
        return $censoredUsername . '@' . $domain;
    }
}
