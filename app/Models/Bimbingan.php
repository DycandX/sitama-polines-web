<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Bimbingan extends Model
{
    use HasFactory;
    protected $table = "bimbingans";
    protected $primaryKey = "bimbingan_id";
    protected $fillable = ['dosen_nip', 'ta_id', 'urutan', 'verified'];
    public $timestamps = false;

    // Admin
    public static function mahasiswaAdmin()
    {
        $mhs = DB::table('mahasiswa')
            ->get();
        // dd($mhs);
        return $mhs;
    }

    public static function taMahasiswaAdmin()
    {
        $ta_mahasiswa = DB::table('tas')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
            ->leftJoin('bimbingans', 'bimbingans.ta_id', '=', 'tas.ta_id')
            ->leftJoin('dosen', 'dosen.dosen_nip', '=', 'bimbingans.dosen_nip')
            ->leftJoin('kode_prodi', 'kode_prodi.prodi_ID', '=', 'mahasiswa.prodi_ID')
            ->leftJoin('ta_sidang', 'ta_sidang.ta_id', '=', 'tas.ta_id')
            ->select(
                'tas.ta_id',
                'tas.ta_judul',
                'tas.tahun_akademik',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'kode_prodi.prodi_ID',
                'kode_prodi.program_studi',
                'ta_sidang.judul_final',
                DB::raw('GROUP_CONCAT(bimbingans.verified ORDER BY bimbingan_id) as dosen_verifikasi'),
                DB::raw('GROUP_CONCAT(bimbingans.dosen_nip ORDER BY bimbingan_id) as dosen_nip'),
                DB::raw('GROUP_CONCAT(dosen.dosen_nama ORDER BY bimbingan_id SEPARATOR "|") as dosen_nama'),
                DB::raw('GROUP_CONCAT(bimbingans.bimbingan_id ORDER BY bimbingan_id) as bimbingan_id')
            )
            ->orderBy('mahasiswa.mhs_nim', 'asc')
            ->groupBy(
                'tas.ta_id',
                'tas.ta_judul',
                'tas.tahun_akademik',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'kode_prodi.prodi_ID',
                'kode_prodi.program_studi',
                'ta_sidang.judul_final'
            )
            ->get();

        $mahasiswa_comp = [];

        foreach ($ta_mahasiswa as $tm) {
            $dosen_nip_array = explode(',', $tm->dosen_nip);
            $dosen_nama_array = explode('|', $tm->dosen_nama);
            $bimbingan_id_array = explode(',', $tm->bimbingan_id);
            $dosen_verifikasi_array = explode(',', $tm->dosen_verifikasi);

            $dosen = [];
            foreach ($dosen_nama_array as $key => $dosen_nama) {
                $dosen[] = [
                    'dosen_nama' => $dosen_nama,
                    'dosen_nip' => $dosen_nip_array[$key],
                    'bimbingan_id' => $bimbingan_id_array[$key],
                    'dosen_verifikasi' => $dosen_verifikasi_array[$key]
                ];
            }
            $tm->dosen = $dosen;

            $mahasiswa_comp[] = $tm;
        }

        $mahasiswa_comp2 = [];

        foreach ($mahasiswa_comp as $item) {
            $ta_id = $item->ta_id;
            $syarat =
                DB::table('dokumen_syarat_ta')
                ->leftJoin('syarat_sidang', function ($join) use ($ta_id) {
                    $join->on('syarat_sidang.dokumen_id', '=', 'dokumen_syarat_ta.dokumen_id')
                        ->where('syarat_sidang.ta_id', '=', $ta_id);
                })
                ->where("dokumen_syarat_ta.is_active", 1)
                ->select(
                    'dokumen_syarat_ta.dokumen_id',
                    'dokumen_syarat_ta.dokumen_syarat',
                    'syarat_sidang.syarat_sidang_id',
                    'syarat_sidang.ta_id',
                    'syarat_sidang.dokumen_file',
                    'syarat_sidang.dokumen_file_original',
                    'syarat_sidang.verified',
                )
                ->get()
                ->toArray();

            $item->syarat = $syarat;

            $mahasiswa_comp2[] = $item;
        }

        return $mahasiswa_comp2;
    }

    public static function taMahasiswaAdmin2()
    {
        return DB::table('tas')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
            ->leftJoin('bimbingans', 'bimbingans.ta_id', '=', 'tas.ta_id')
            ->leftJoin('dosen', 'dosen.dosen_nip', '=', 'bimbingans.dosen_nip')
            ->leftJoin('kode_prodi', 'kode_prodi.prodi_ID', '=', 'mahasiswa.prodi_ID')
            ->leftJoin('ta_sidang', 'ta_sidang.ta_id', '=', 'tas.ta_id')
            ->select(
                'tas.ta_id',
                'tas.ta_judul',
                'tas.tahun_akademik',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'kode_prodi.program_studi',
                'ta_sidang.judul_final',
                DB::raw('GROUP_CONCAT(bimbingans.dosen_nip ORDER BY bimbingan_id) as dosen_nip'),
                DB::raw('GROUP_CONCAT(dosen.dosen_nama ORDER BY bimbingan_id SEPARATOR "|") as dosen_nama'),
                DB::raw('GROUP_CONCAT(bimbingans.bimbingan_id ORDER BY bimbingan_id) as bimbingan_id')
            )
            ->orderBy('mahasiswa.mhs_nim', 'asc')
            ->groupBy(
                'tas.ta_id',
                'tas.ta_judul',
                'tas.tahun_akademik',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'kode_prodi.program_studi',
                'ta_sidang.judul_final'
            );
    }

    // Mahasiswa
    public static function Mahasiswa($id)
    {
        $mahasiswa = DB::table('mahasiswa')
            ->join('kode_prodi', 'mahasiswa.prodi_ID', '=', 'kode_prodi.prodi_ID')
            ->join('users', 'mahasiswa.email', '=', 'users.email')
            ->leftJoin('tas', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
            ->leftJoin('bimbingans', 'tas.ta_id', '=', 'bimbingans.ta_id')
            ->leftJoin('dosen', 'bimbingans.dosen_nip', '=', 'dosen.dosen_nip')
            ->leftJoin('ta_sidang', 'ta_sidang.ta_id', '=', 'tas.ta_id')
            ->leftJoin('penilaian_penguji', 'penilaian_penguji.ta_sidang_id', '=', 'ta_sidang.ta_sidang_id')
            ->leftJoin('dosen as dosen_penguji', 'penilaian_penguji.dosen_nip', '=', 'dosen_penguji.dosen_nip')
            ->leftJoin('dosen as dosen_sekre', 'ta_sidang.dosen_nip', '=', 'dosen_sekre.dosen_nip')
            ->leftJoin('jadwal_sidang', 'jadwal_sidang.jadwal_id', '=', 'ta_sidang.jadwal_id')
            ->leftJoin('sesi_ta', 'sesi_ta.sesi_id', '=', 'jadwal_sidang.sesi_id')
            ->leftJoin('ruangan_ta', 'ruangan_ta.ruangan_id', '=', 'jadwal_sidang.ruangan_id')
            ->select(
                'users.id',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'kode_prodi.program_studi',
                'tas.tahun_akademik',
                'tas.ta_id',
                'tas.ta_judul',
                'dosen_sekre.dosen_nama as sekre',
                'ta_sidang.judul_final',
                'ta_sidang.revisi_file',
                'ta_sidang.revisi_file_original',
                'jadwal_sidang.tgl_sidang',
                'sesi_ta.sesi_nama',
                'sesi_ta.sesi_waktu_mulai',
                'sesi_ta.sesi_waktu_selesai',
                'sesi_ta.sesi_waktu_mulai_jumat',
                'sesi_ta.sesi_waktu_selesai_jumat',
                'ruangan_ta.ruangan_nama',
                DB::raw('GROUP_CONCAT(distinct(bimbingans.dosen_nip) ORDER BY bimbingans.bimbingan_id) as dosen_nip'),
                DB::raw('GROUP_CONCAT(distinct(dosen.dosen_nama) ORDER BY bimbingans.bimbingan_id SEPARATOR "|") as dosen_nama'),
                DB::raw('GROUP_CONCAT(distinct(bimbingans.bimbingan_id) ORDER BY bimbingans.bimbingan_id) as bimbingan_id'),
                DB::raw('GROUP_CONCAT(distinct(bimbingans.urutan) ORDER BY bimbingans.bimbingan_id) as urutan'),
                DB::raw('GROUP_CONCAT(bimbingans.verified ORDER BY bimbingans.bimbingan_id) as verified'),
                DB::raw('GROUP_CONCAT(distinct(dosen_penguji.dosen_nama) ORDER BY penilaian_penguji.urutan SEPARATOR "|" ) as penguji_nama')
            )

            ->where('users.id', '=', $id)
            ->groupBy(
                'users.id',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'kode_prodi.program_studi',
                'tas.tahun_akademik',
                'tas.ta_id',
                'tas.ta_judul',
                'sekre',
                'ta_sidang.judul_final',
                'ta_sidang.revisi_file',
                'ta_sidang.revisi_file_original',
                'jadwal_sidang.tgl_sidang',
                'sesi_ta.sesi_nama',
                'sesi_ta.sesi_waktu_mulai',
                'sesi_ta.sesi_waktu_selesai',
                'sesi_ta.sesi_waktu_mulai_jumat',
                'sesi_ta.sesi_waktu_selesai_jumat',
                'ruangan_ta.ruangan_nama',
            )
            ->first();

        if (isset($mahasiswa->dosen_nip) && $mahasiswa->dosen_nama && $mahasiswa->bimbingan_id) {
            $dosen_nip_array = explode(',', $mahasiswa->dosen_nip);
            $dosen_nama_array = explode('|', $mahasiswa->dosen_nama);
            $bimbingan_id_array = explode(',', $mahasiswa->bimbingan_id);
            $verified_array = explode(',', $mahasiswa->verified);
            $urutan_array = explode(',', $mahasiswa->urutan);
            $penguji_nama_array = explode('|', $mahasiswa->penguji_nama);


            $dosen = [];
            foreach ($dosen_nama_array as $key => $dosen_nama) {
                $dosen[] = [
                    'dosen_nama' => $dosen_nama,
                    'dosen_nip' => $dosen_nip_array[$key],
                    'bimbingan_id' => $bimbingan_id_array[$key],
                    'verified' => $verified_array[$key],
                    'urutan' => $urutan_array[$key]
                ];
            }


            $penguji = [];
            foreach ($penguji_nama_array as $key => $penguji_nama) {
                $penguji[] = [
                    'penguji_nama' => $penguji_nama
                ];
            }

            $mahasiswa->dosen = $dosen;
            $mahasiswa->penguji = $penguji;
        }
        return $mahasiswa;
    }

    // Dosen
    public static function ta_mahasiswa()
    {
        $ta_mahasiswa = DB::table('tas')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
            ->leftJoin('bimbingans', 'bimbingans.ta_id', '=', 'tas.ta_id')
            ->leftJoin('dosen', 'dosen.dosen_nip', '=', 'bimbingans.dosen_nip')
            ->select(
                'tas.ta_id',
                'tas.ta_judul',
                'tas.tahun_akademik',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                DB::raw('GROUP_CONCAT(bimbingans.dosen_nip ORDER BY bimbingan_id) as dosen_nip'),
                DB::raw('GROUP_CONCAT(dosen.dosen_nama ORDER BY bimbingan_id SEPARATOR "|") as dosen_nama'),
                DB::raw('GROUP_CONCAT(bimbingans.bimbingan_id ORDER BY bimbingan_id) as bimbingan_id')
            )
            ->orderBy('mahasiswa.mhs_nim', 'asc')
            ->groupBy(
                'tas.ta_id',
                'tas.ta_judul',
                'tas.tahun_akademik',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama'
            )
            ->get();

        $mahasiswa_comp = [];

        foreach ($ta_mahasiswa as $tm) {
            $dosen_nip_array = explode(',', $tm->dosen_nip);
            $dosen_nama_array = explode('|', $tm->dosen_nama);
            $bimbingan_id_array = explode(',', $tm->bimbingan_id);

            $dosen = [];
            foreach ($dosen_nama_array as $key => $dosen_nama) {
                $dosen[] = [
                    'dosen_nama' => $dosen_nama,
                    'dosen_nip' => $dosen_nip_array[$key],
                    'bimbingan_id' => $bimbingan_id_array[$key]
                ];
            }
            $tm->dosen = $dosen;
            $mahasiswa_comp[] = $tm;
        }
        return $mahasiswa_comp;
    }

    public static function ta_mahasiswa2()
    {
        $email = Auth::user()->email;

        $ta_mahasiswa = DB::table('tas')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
            ->leftJoin('bimbingans', 'bimbingans.ta_id', '=', 'tas.ta_id')
            ->leftJoin('dosen', 'dosen.dosen_nip', '=', 'bimbingans.dosen_nip')
            ->select(
                'tas.ta_id',
                'tas.ta_judul',
                'tas.tahun_akademik',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                DB::raw('GROUP_CONCAT(bimbingans.dosen_nip ORDER BY bimbingan_id) as dosen_nip'),
                DB::raw('GROUP_CONCAT(dosen.dosen_nama ORDER BY bimbingan_id SEPARATOR "|") as dosen_nama'),
                DB::raw('GROUP_CONCAT(bimbingans.bimbingan_id ORDER BY bimbingan_id) as bimbingan_id'),
                DB::raw('GROUP_CONCAT(bimbingans.urutan ORDER BY bimbingan_id) as urutan'),
            )
            ->orderBy('mahasiswa.mhs_nim', 'asc')
            ->groupBy(
                'tas.ta_id',
                'tas.ta_judul',
                'tas.tahun_akademik',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama'
            )
            ->where('dosen.email', $email)
            ->get();

        $mahasiswa_comp = [];

        foreach ($ta_mahasiswa as $tm) {
            $dosen_nip_array = explode(',', $tm->dosen_nip);
            $dosen_nama_array = explode('|', $tm->dosen_nama);
            $bimbingan_id_array = explode(',', $tm->bimbingan_id);
            $urutan_array = explode(',', $tm->urutan);

            $dosen = [];
            foreach ($dosen_nama_array as $key => $dosen_nama) {
                $dosen[] = [
                    'dosen_nama' => $dosen_nama,
                    'dosen_nip' => $dosen_nip_array[$key],
                    'bimbingan_id' => $bimbingan_id_array[$key],
                    'urutan' => $urutan_array[$key]
                ];
            }
            $tm->dosen = $dosen;
            $mahasiswa_comp[] = $tm;
        }
        return $mahasiswa_comp;
    }

    public function tas()
    {
        return $this->belongsTo(Ta::class, 'ta_id');
    }

    public function bimbinganLog()
    {
        return $this->hasMany(BimbinganLog::class, 'bimbingan_id');
    }

    public static function getBimbinganData($id, $sebagai)
    {
        return DB::table('bimbingans')
            ->join('tas', 'bimbingans.ta_id', '=', 'tas.ta_id')
            ->join('mahasiswa', 'tas.mhs_nim', '=', 'mahasiswa.mhs_nim')
            ->join('dosen', 'bimbingans.dosen_nip', '=', 'dosen.dosen_nip')
            ->join('bimbingan_log', 'bimbingans.bimbingan_id', '=', 'bimbingan_log.bimbingan_id')
            ->where('tas.ta_id', $id)
            ->where('bimbingans.urutan', $sebagai)
            ->select('bimbingan_log.bimb_judul', 'bimbingan_log.bimb_desc', 'bimbingan_log.bimb_tgl', 'bimbingans.dosen_nip', 'dosen.file_ttd', 'mahasiswa.mhs_nim', 'mahasiswa.mhs_nama', 'tas.ta_judul')
            ->get();
    }

    public static function getDosenName($dosenNip)
    {
        return DB::table('dosen')->where('dosen_nip', $dosenNip)->value('dosen_nama');
    }
}
