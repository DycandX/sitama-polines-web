<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UjianSidang extends Model
{
    use HasFactory;

    protected $table = 'bimbingans'; // Nama tabel jika tidak mengikuti konvensi Laravel
    protected $primaryKey = 'ta_id';

    public static function ta_mahasiswa()
    {
        $user = Auth::user(); // Mengambil user yang sedang login

        $ta_mahasiswa = DB::table('tas')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
            ->join('kode_prodi', 'kode_prodi.prodi_ID', '=', 'mahasiswa.prodi_ID')
            ->leftJoin('bimbingans', 'bimbingans.ta_id', '=', 'tas.ta_id')
            ->leftJoin('bimbingans as user_bimbingan', 'user_bimbingan.ta_id', '=', 'tas.ta_id') //pembimbing
            ->leftJoin('dosen', 'dosen.dosen_nip', '=', 'bimbingans.dosen_nip')
            ->leftJoin('dosen as user_dosen', 'user_dosen.dosen_nip', '=', 'user_bimbingan.dosen_nip')
            ->leftJoin('ta_sidang', 'ta_sidang.ta_id', '=', 'tas.ta_id')
            ->leftJoin('dosen as sekre', 'sekre.dosen_nip', '=', 'ta_sidang.dosen_nip')
            ->leftJoin('penilaian_penguji', 'penilaian_penguji.ta_sidang_id', '=', 'ta_sidang.ta_sidang_id')
            ->leftJoin('penilaian_penguji as user_penilaian_penguji', 'user_penilaian_penguji.ta_sidang_id', '=', 'ta_sidang.ta_sidang_id')
            ->leftJoin('dosen as dosen_penguji', 'dosen_penguji.dosen_nip', '=', 'penilaian_penguji.dosen_nip') //penguji
            ->leftJoin('dosen as user_dosen_penguji', 'user_dosen_penguji.dosen_nip', '=', 'user_penilaian_penguji.dosen_nip')
            ->leftJoin('jadwal_sidang', 'jadwal_sidang.jadwal_id', '=', 'ta_sidang.jadwal_id')
            ->leftJoin('sesi_ta', 'sesi_ta.sesi_id', '=', 'jadwal_sidang.sesi_id')
            ->leftJoin('ruangan_ta', 'ruangan_ta.ruangan_id', '=', 'jadwal_sidang.ruangan_id')
            ->where('dosen.email', '=', $user->email)
            ->orWhere('dosen_penguji.email', '=', $user->email)
            ->orWhere('sekre.email', '=', $user->email)
            ->select(
                'tas.ta_id',
                'tas.ta_judul',
                'tas.tahun_akademik',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'mahasiswa.prodi_ID',
                'kode_prodi.program_studi',
                'jadwal_sidang.jadwal_id',
                'jadwal_sidang.tgl_sidang',
                'ruangan_ta.ruangan_nama',
                'sesi_ta.sesi_nama',
                'sesi_ta.sesi_waktu_mulai',
                'sesi_ta.sesi_waktu_selesai',
                'sesi_ta.sesi_waktu_mulai_jumat',
                'sesi_ta.sesi_waktu_selesai_jumat',
                'ta_sidang.nilai_akhir',
                'ta_sidang.ta_sidang_id',
                'sekre.dosen_nama as sekretaris',
                'ta_sidang.status_lulus',
                'sekre.dosen_nip AS sekre_nip',
                DB::raw('GROUP_CONCAT(DISTINCT(bimbingans.dosen_nip) ORDER BY bimbingans.bimbingan_id) as dosen_nip'),
                DB::raw('GROUP_CONCAT(DISTINCT(dosen.dosen_nama) ORDER BY bimbingans.bimbingan_id SEPARATOR "|") as dosen_nama'),
                DB::raw('GROUP_CONCAT(DISTINCT(bimbingans.bimbingan_id) ORDER BY bimbingans.bimbingan_id) as bimbingan_id'),
                DB::raw('GROUP_CONCAT(DISTINCT(bimbingans.urutan) ORDER BY bimbingans.bimbingan_id) as urutan'),
                DB::raw('GROUP_CONCAT(DISTINCT(penilaian_penguji.penilaian_id) ORDER BY penilaian_penguji.penilaian_id) as penilaian_penguji_id'),
                DB::raw('GROUP_CONCAT(DISTINCT(penilaian_penguji.urutan) ORDER BY penilaian_penguji.penilaian_id) as urutan_penguji'),
                DB::raw('GROUP_CONCAT(DISTINCT(dosen_penguji.dosen_nama) ORDER BY penilaian_penguji.penilaian_id SEPARATOR "|") as dosen_penguji_nama'),
                DB::raw('GROUP_CONCAT(DISTINCT(penilaian_penguji.dosen_nip) ORDER BY penilaian_penguji.penilaian_id) as dosen_penguji_nip'),

                DB::raw('GROUP_CONCAT(DISTINCT(user_bimbingan.dosen_nip) ORDER BY user_bimbingan.bimbingan_id) as user_dosen_nip'),
                DB::raw('GROUP_CONCAT(DISTINCT(user_dosen.dosen_nama) ORDER BY user_bimbingan.bimbingan_id SEPARATOR "|") as user_dosen_nama'),
                DB::raw('GROUP_CONCAT(DISTINCT(user_bimbingan.bimbingan_id) ORDER BY user_bimbingan.bimbingan_id) as user_bimbingan_id'),
                DB::raw('GROUP_CONCAT(DISTINCT(user_bimbingan.urutan) ORDER BY user_bimbingan.bimbingan_id SEPARATOR "|") as user_urutan'),
                DB::raw('GROUP_CONCAT(DISTINCT(user_penilaian_penguji.penilaian_id) ORDER BY user_penilaian_penguji.penilaian_id) as user_penilaian_penguji_id'),
                DB::raw('GROUP_CONCAT(DISTINCT(user_penilaian_penguji.urutan) ORDER BY user_penilaian_penguji.penilaian_id) as user_urutan_penguji'),
                DB::raw('GROUP_CONCAT(DISTINCT(user_dosen_penguji.dosen_nama) ORDER BY user_penilaian_penguji.penilaian_id SEPARATOR "|") as user_dosen_penguji_nama'),
                DB::raw('GROUP_CONCAT(DISTINCT(user_penilaian_penguji.dosen_nip) ORDER BY user_penilaian_penguji.penilaian_id) as user_dosen_penguji_nip'),

            )
            ->orderBy('jadwal_sidang.jadwal_id', 'desc')
            ->orderBy('mahasiswa.mhs_nama', 'asc')
            ->groupBy(
                'tas.ta_id',
                'tas.ta_judul',
                'tas.tahun_akademik',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'mahasiswa.prodi_ID',
                'kode_prodi.program_studi',
                'jadwal_sidang.jadwal_id',
                'jadwal_sidang.tgl_sidang',
                'ruangan_ta.ruangan_nama',
                'sesi_ta.sesi_nama',
                'sesi_ta.sesi_waktu_mulai',
                'sesi_ta.sesi_waktu_selesai',
                'sesi_ta.sesi_waktu_mulai_jumat',
                'sesi_ta.sesi_waktu_selesai_jumat',
                'ta_sidang.nilai_akhir',
                'ta_sidang.ta_sidang_id',
                'sekretaris',
                'ta_sidang.status_lulus',
                'sekre_nip'
            )
            ->get();


        $mahasiswa_comp = [];

        foreach ($ta_mahasiswa as $tm) {
            $dosen_nip_array = explode(',', $tm->dosen_nip);
            $dosen_nama_array = explode('|', $tm->dosen_nama);
            $bimbingan_id_array = explode(',', $tm->bimbingan_id);
            $penilaian_penguji = explode('|', $tm->dosen_penguji_nama);
            $urutan_penguji = explode(',', $tm->urutan_penguji);
            $nip_penguji = explode(',', $tm->dosen_penguji_nip);

            $dosen_nip_array2 = explode(',', $tm->user_dosen_nip);
            $dosen_nama_array2 = explode('|', $tm->user_dosen_nama);
            $bimbingan_id_array2 = explode(',', $tm->user_bimbingan_id);
            $penilaian_penguji2 = explode('|', $tm->user_dosen_penguji_nama);
            $urutan_penguji2 = explode(',', $tm->user_urutan_penguji);
            $nip_penguji2 = explode(',', $tm->user_dosen_penguji_nip);


            //dosen_pembimbing
            $dosen = [];
            foreach ($dosen_nama_array as $key => $dosen_nama) {
                $dosen[] = [
                    'dosen_nama' => $dosen_nama,
                    'dosen_nip' => $dosen_nip_array[$key],
                    'bimbingan_id' => $bimbingan_id_array[$key],
                ];
            }
            $tm->dosen = $dosen;

            $user_dosen = [];
            foreach ($dosen_nama_array2 as $key => $user_dosen_nama) {
                $user_dosen[] = [
                    'user_dosen_nama' => $user_dosen_nama,
                    'user_dosen_nip' => $dosen_nip_array2[$key],
                    'user_bimbingan_id' => $bimbingan_id_array2[$key],

                ];
            }
            $tm->user_dosen = $user_dosen;

            //dosen_penguji
            $dosen_penguji = [];
            foreach ($penilaian_penguji as $key => $dosen_nama) {
                $dosen_penguji[] = [
                    'dosen_nama' => $dosen_nama,
                    'urutan' => $urutan_penguji[$key],
                    'dosen_nip' => $nip_penguji[$key],
                ];
            }
            $tm->dosen_penguji = $dosen_penguji;

            $user_dosen_penguji = [];
            foreach ($penilaian_penguji2 as $key => $user_dosen_nama) {
                $user_dosen_penguji[] = [
                    'user_dosen_nama' => $user_dosen_nama,
                    'user_urutan' => $urutan_penguji2[$key],
                    'user_dosen_nip' => $nip_penguji2[$key],
                ];
            }
            $tm->user_dosen_penguji = $user_dosen_penguji;


            $mahasiswa_comp[] = $tm;
        }

        return $mahasiswa_comp;
    }
}
