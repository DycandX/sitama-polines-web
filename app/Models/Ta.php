<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Ta extends Model
{
    use HasFactory;
    protected $table = "tas";
    protected $primaryKey = "ta_id";
    protected $fillable = ['mhs_nim', 'ta_judul', 'tahun_akademik'];
    public $timestamps = false;

    // Admin
    public static function detailMahasiswa($ta_id)
    {
        $ta_mahasiswa = DB::table('tas')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
            ->leftJoin('bimbingans', 'bimbingans.ta_id', '=', 'tas.ta_id')
            ->leftJoin('dosen', 'dosen.dosen_nip', '=', 'bimbingans.dosen_nip')
            ->leftJoin('ta_sidang', 'ta_sidang.ta_id', '=', 'tas.ta_id')
            ->select(
                'tas.ta_id',
                'tas.ta_judul',
                'tas.tahun_akademik',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'ta_sidang.judul_final',
                DB::raw('GROUP_CONCAT(bimbingans.bimbingan_id ORDER BY bimbingan_id) as bimbingan_id'),
                DB::raw('GROUP_CONCAT(bimbingans.dosen_nip ORDER BY bimbingan_id) as dosen_nip'),
                DB::raw('GROUP_CONCAT(dosen.dosen_nama ORDER BY bimbingan_id SEPARATOR "|") as dosen_nama'),
                DB::raw('GROUP_CONCAT(bimbingans.urutan ORDER BY bimbingan_id) as urutan'),
                DB::raw('GROUP_CONCAT(dosen.file_ttd ORDER BY bimbingan_id SEPARATOR "|") as file_ttd') // Menambahkan kolom file_ttd
            )
            ->orderBy('mahasiswa.mhs_nim', 'asc')
            ->groupBy(
                'tas.ta_id',
                'tas.ta_judul',
                'tas.tahun_akademik',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'ta_sidang.judul_final'
            )
            ->where('tas.ta_id', '=', $ta_id)
            ->first();

        if ($ta_mahasiswa) {
            $bimbingan_id_array = explode(',', $ta_mahasiswa->bimbingan_id);
            $dosen_nip_array = explode(',', $ta_mahasiswa->dosen_nip);
            $dosen_nama_array = explode('|', $ta_mahasiswa->dosen_nama);
            $urutan_array = explode(',', $ta_mahasiswa->urutan);
            $file_ttd_array = explode('|', $ta_mahasiswa->file_ttd); // Tambahkan array file_ttd

            $dosen = [];
            foreach ($dosen_nama_array as $key => $dosen_nama) {
                $dosen[] = [
                    'bimbingan_id' => $bimbingan_id_array[$key] ?? null,
                    'dosen_nip' => $dosen_nip_array[$key] ?? null,
                    'dosen_nama' => $dosen_nama,
                    'urutan' => $urutan_array[$key] ?? null,
                    'file_ttd' => $file_ttd_array[$key] ?? null // Tambahkan file_ttd
                ];
            }
            $ta_mahasiswa->dosen = $dosen;
        }

        return $ta_mahasiswa;
    }


    public static function taSidang2()
    {
        $taSidang = DB::table('ta_sidang')
            ->Join('tas', 'tas.ta_id', '=', 'ta_sidang.ta_id')
            ->Join('mahasiswa', 'tas.mhs_nim', '=', 'mahasiswa.mhs_nim')
            ->Join('kode_prodi', 'kode_prodi.prodi_ID', '=', 'mahasiswa.prodi_ID')
            ->Join('bimbingans', 'tas.ta_id', '=', 'bimbingans.ta_id')
            ->leftJoin('penilaian_penguji', 'penilaian_penguji.ta_sidang_id', '=', 'ta_sidang.ta_sidang_id')
            ->leftJoin('dosen', 'bimbingans.dosen_nip', '=', 'dosen.dosen_nip')
            ->leftJoin('dosen as dosen_sekre', 'ta_sidang.dosen_nip', '=', 'dosen_sekre.dosen_nip')
            ->leftJoin('dosen as dosen_penguji', 'penilaian_penguji.dosen_nip', '=', 'dosen_penguji.dosen_nip')
            ->Join('jadwal_sidang', 'jadwal_sidang.jadwal_id', '=', 'ta_sidang.jadwal_id')
            ->Join('sesi_ta', 'sesi_ta.sesi_id', '=', 'jadwal_sidang.sesi_id')
            ->Join('ruangan_ta', 'ruangan_ta.ruangan_id', '=', 'jadwal_sidang.ruangan_id')
            ->select(
                'ta_sidang.ta_sidang_id',
                'ta_sidang.judul_final',
                'kode_prodi.prodi_ID',
                'kode_prodi.program_studi',
                'ta_sidang.nilai_akhir',
                'ta_sidang.status_lulus',
                'ta_sidang.verified',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'tas.tahun_akademik',
                'tas.ta_id',
                'tas.ta_judul',
                'jadwal_sidang.tgl_sidang',
                'sesi_ta.sesi_nama',
                'sesi_ta.sesi_waktu_mulai',
                'sesi_ta.sesi_waktu_selesai',
                'sesi_ta.sesi_waktu_mulai_jumat',
                'sesi_ta.sesi_waktu_selesai_jumat',
                'ruangan_ta.ruangan_nama',
                'dosen_sekre.dosen_nama as sekre',
                DB::raw('GROUP_CONCAT(distinct(bimbingans.dosen_nip) ORDER BY bimbingans.bimbingan_id) as dosen_nip'),
                DB::raw('GROUP_CONCAT(distinct(dosen.dosen_nama) ORDER BY bimbingans.bimbingan_id SEPARATOR "|") as dosen_nama'),
                DB::raw('GROUP_CONCAT(distinct(bimbingans.bimbingan_id) ORDER BY bimbingans.bimbingan_id) as bimbingan_id'),
                DB::raw('GROUP_CONCAT(distinct(penilaian_penguji.dosen_nip) ORDER BY penilaian_penguji.urutan) as dosen_nip_penguji'),
                DB::raw('GROUP_CONCAT(distinct(dosen_penguji.dosen_nama) ORDER BY penilaian_penguji.urutan SEPARATOR "|") as penguji_nama'),
                DB::raw('GROUP_CONCAT(distinct(penilaian_penguji.urutan) ORDER BY penilaian_penguji.urutan) as urutan')
            )
            ->groupBy(
                'ta_sidang.ta_sidang_id',
                'ta_sidang.judul_final',
                'kode_prodi.prodi_ID',
                'kode_prodi.program_studi',
                'ta_sidang.nilai_akhir',
                'ta_sidang.status_lulus',
                'ta_sidang.verified',
                'mahasiswa.mhs_nim',
                'mahasiswa.mhs_nama',
                'tas.tahun_akademik',
                'tas.ta_id',
                'tas.ta_judul',
                'jadwal_sidang.tgl_sidang',
                'sesi_ta.sesi_nama',
                'sesi_ta.sesi_waktu_mulai',
                'sesi_ta.sesi_waktu_selesai',
                'sesi_ta.sesi_waktu_mulai_jumat',
                'sesi_ta.sesi_waktu_selesai_jumat',
                'ruangan_ta.ruangan_nama',
                'sekre'
            )
            ->orderBy('jadwal_sidang.tgl_sidang', 'DESC')
            ->orderBy('sesi_ta.sesi_id', 'DESC')
            ->get();

        $array_dosen = [];
        foreach ($taSidang as $mahasiswa) {
            $dosen_nip_array = explode(',', $mahasiswa->dosen_nip);
            $dosen_nama_array = explode('|', $mahasiswa->dosen_nama);
            $bimbingan_id_array = explode(',', $mahasiswa->bimbingan_id);
            $dosen_nip_penguji_array = explode(',', $mahasiswa->dosen_nip_penguji);
            $penguji_nama_array = explode('|', $mahasiswa->penguji_nama);
            $urutan_array = explode(',', $mahasiswa->urutan);

            $dosen = [];
            foreach ($dosen_nama_array as $key => $dosen_nama) {
                $dosen_nip = isset($dosen_nip_array[$key]) ? $dosen_nip_array[$key] : null;
                $bimbingan_id = isset($bimbingan_id_array[$key]) ? $bimbingan_id_array[$key] : null;
                $dosen[] = [
                    'dosen_nama' => $dosen_nama,
                    'dosen_nip' => $dosen_nip,
                    'bimbingan_id' => $bimbingan_id,
                ];
            }

            $penguji = [];
            foreach ($penguji_nama_array as $key => $penguji_nama) {
                $dosen_nip_penguji = isset($dosen_nip_penguji_array[$key]) ? $dosen_nip_penguji_array[$key] : null;
                $urutan = isset($urutan_array[$key]) ? $urutan_array[$key] : null;
                $penguji[] = [
                    'dosen_nip_penguji' => $dosen_nip_penguji,
                    'penguji_nama' => $penguji_nama,
                    'urutan' => $urutan,
                ];
                // dd($penguji);
            }

            $mahasiswa->dosen = $dosen;
            $mahasiswa->penguji = $penguji;

            if (!empty($mahasiswa->tgl_sidang) && empty($mahasiswa->nilai_akhir)) {
                $mahasiswa->status = '1';
            } elseif (!empty($mahasiswa->tgl_sidang) && !empty($mahasiswa->nilai_akhir)) {
                $mahasiswa->status = '2';
            } else {
                $mahasiswa->status = '0';
            }

            $array_dosen[] = $mahasiswa;
        }

        return $array_dosen;
    }

    // Mahasiswa
    public static function dataTa($id)
    {
        $dataTa = DB::table('tas')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
            ->join('users', 'users.email', '=', 'mahasiswa.email')
            ->where('users.id', '=', $id)
            ->select('tas.*')
            ->first();

        return $dataTa;
    }

    // Dosen
    public static function TaSidang()
    {
        $taSidang = DB::table('ta_sidang')
            ->join('tas', 'ta_sidang.ta_id', '=', 'tas.ta_id')
            ->join('jadwal_sidang', 'ta_sidang.jadwal_id', '=', 'jadwal_sidang.jadwal_id')
            ->join('sesi_ta', 'jadwal_sidang.sesi_id', '=', 'sesi_ta.sesi_id')
            ->join('ruangan_ta', 'jadwal_sidang.ruangan_id', '=', 'ruangan_ta.ruangan_id')
            ->join('bimbingans', 'tas.ta_id', '=', 'bimbingans.ta_id')
            ->join('dosen', 'bimbingans.dosen_nip', '=', 'dosen.dosen_nip')
            ->join('mahasiswa', 'tas.mhs_nim', '=', 'mahasiswa.mhs_nim')
            ->select('ta_sidang.*', 'tas.mhs_nim', 'mahasiswa.mhs_nama', 'sesi_ta.sesi_waktu_mulai', 'sesi_ta.sesi_waktu_selesai', 'ruangan_ta.ruangan_nama', 'dosen.dosen_nama')
            ->orderBy('mahasiswa.mhs_nim', 'asc')
            ->get();

        return $taSidang;
    }

    public static function CekTahunAjaran()
    {
        $tahun_ajaran = DB::table('master_ta')
            ->where('status', 1)
            ->first();

        return $tahun_ajaran;
    }

    public static function NoSk($tahun_ajaran,$prodi)
    {
        $tahun_ajaran = DB::table('no_sk')
            ->where('tahun_ajaran', $tahun_ajaran)
            ->where('prodi', $prodi)
            ->first();

        return $tahun_ajaran;
    }

    public static function TaId($nim)
    {
        $tas = DB::table('tas_mahasiswa')
            ->where('mhs_nim', $nim)
            ->first();

        return $tas;
    }

    public static function IsKelompok($taid)
    {
        $tas = DB::table('tas_mahasiswa')
            ->where('ta_id', $taid)
            ->count();

        return $tas;
    }

    public static function SatuKelompok($id)
    {
        $satukelompok = DB::table('tas_mahasiswa')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas_mahasiswa.mhs_nim')
            ->where('tas_mahasiswa.ta_id', '=', $id)
            ->select('mahasiswa.mhs_nim', 'mahasiswa.mhs_nama')
            ->get();

        return $satukelompok;
    }

    public static function detailKelayakan($ta_id)
    {
        $user = Auth::user();

        $ta = DB::table('tas')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
            ->join('ta_sidang', 'ta_sidang.ta_id', '=', 'tas.ta_id')
            ->join('jadwal_sidang', 'jadwal_sidang.jadwal_id', '=', 'ta_sidang.jadwal_id')
            ->join('ruangan_ta', 'ruangan_ta.ruangan_id', '=', 'jadwal_sidang.ruangan_id')
            ->join('bimbingans', 'bimbingans.ta_id', '=', 'tas.ta_id')
            ->join('dosen', 'dosen.dosen_nip', '=', 'bimbingans.dosen_nip')
            ->join('users', 'users.email', '=', 'dosen.email')
            ->where('tas.ta_id', $ta_id)
            ->where('users.email', $user->email)
            ->select('*')
            ->first();

        return $ta;
    }
    public static function detailKelayakan2($ta_id)
    {
        $user = Auth::user();

        $ta = DB::table('tas')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
            ->join('ta_sidang', 'ta_sidang.ta_id', '=', 'tas.ta_id')
            ->join('jadwal_sidang', 'jadwal_sidang.jadwal_id', '=', 'ta_sidang.jadwal_id')
            ->join('ruangan_ta', 'ruangan_ta.ruangan_id', '=', 'jadwal_sidang.ruangan_id')
            ->join('penilaian_penguji', 'penilaian_penguji.ta_sidang_id', '=', 'ta_sidang.ta_sidang_id')
            ->join('dosen', 'dosen.dosen_nip', '=', 'penilaian_penguji.dosen_nip')
            ->join('users', 'users.email', '=', 'dosen.email')
            ->where('tas.ta_id', $ta_id)
            ->where('users.email', $user->email)
            ->select('*')
            ->first();

        return $ta;
    }

    public static function dataujian($ta_id)
    {
        $user = Auth::user();

        $ta = DB::table('tas')
            ->join('mahasiswa', 'mahasiswa.mhs_nim', '=', 'tas.mhs_nim')
            ->join('ta_sidang', 'ta_sidang.ta_id', '=', 'tas.ta_id')
            ->join('jadwal_sidang', 'jadwal_sidang.jadwal_id', '=', 'ta_sidang.jadwal_id')
            ->join('ruangan_ta', 'ruangan_ta.ruangan_id', '=', 'jadwal_sidang.ruangan_id')
            ->join('sesi_ta','sesi_ta.sesi_id','jadwal_sidang.sesi_id')
            ->join('penilaian_penguji AS penguji1', function($join) {
                $join->on('penguji1.ta_sidang_id', '=', 'ta_sidang.ta_sidang_id')
                     ->where('penguji1.urutan', '=', 1);
            })
            ->join('dosen AS dosen_penguji1', 'dosen_penguji1.dosen_nip', '=', 'penguji1.dosen_nip')
            ->join('penilaian_penguji AS penguji2', function($join) {
                $join->on('penguji2.ta_sidang_id', '=', 'ta_sidang.ta_sidang_id')
                     ->where('penguji2.urutan', '=', 2);
            })
            ->join('dosen AS dosen_penguji2', 'dosen_penguji2.dosen_nip', '=', 'penguji2.dosen_nip')
            ->join('penilaian_penguji AS penguji3', function($join) {
                $join->on('penguji3.ta_sidang_id', '=', 'ta_sidang.ta_sidang_id')
                     ->where('penguji3.urutan', '=', 3);
            })
            ->join('dosen AS dosen_penguji3', 'dosen_penguji3.dosen_nip', '=', 'penguji3.dosen_nip')
            ->join('dosen AS sekretaris', 'sekretaris.dosen_nip', '=', 'ta_sidang.dosen_nip')
            ->select(
                'tas.*',
                'ta_sidang.*',
                'mahasiswa.*',
                'jadwal_sidang.*',
                'ruangan_ta.*',
                'sesi_ta.*',
                'dosen_penguji1.dosen_nip AS penguji1_nip',
                'dosen_penguji1.dosen_nama AS penguji1_nama',
                'dosen_penguji1.file_ttd AS penguji1_ttd_path',
                'dosen_penguji2.dosen_nip AS penguji2_nip',
                'dosen_penguji2.dosen_nama AS penguji2_nama',
                'dosen_penguji2.file_ttd AS penguji2_ttd_path',
                'dosen_penguji3.dosen_nip AS penguji3_nip',
                'dosen_penguji3.dosen_nama AS penguji3_nama',
                'dosen_penguji3.file_ttd AS penguji3_ttd_path',
                'sekretaris.dosen_nip AS sekretaris_nip',
                'sekretaris.dosen_nama AS sekretaris_nama',
                'sekretaris.file_ttd AS sekretaris_ttd_path'
            )
            ->where('tas.ta_id', $ta_id)
            ->first();

        return $ta;
    }

    public static function unsur_nilai_pembimbing()
    {

        $nilai_pembimbing = DB::table('unsur_nilai_pembimbing')
            ->select('*');

        return $nilai_pembimbing;
    }

    public static function unsur_nilai_penguji()
    {

        $nilai_penguji = DB::table('unsur_nilai_penguji')
            ->select('*');

        return $nilai_penguji;
    }

    public static function getNilaiPenguji($ta_sidang_id, $dosen_nip)
    {
        $nilaiPenguji = DB::table('penilaian_penguji_detail')
            ->join('unsur_nilai_penguji', 'penilaian_penguji_detail.nilai_id', '=', 'unsur_nilai_penguji.nilai_id')
            ->where('penilaian_penguji_detail.ta_sidang_id', $ta_sidang_id)
            ->where('penilaian_penguji_detail.dosen_nip', $dosen_nip)
            ->select(DB::raw('SUM(penilaian_penguji_detail.berinilai * unsur_nilai_penguji.bobot) as total_nilai'))
            ->first();

        return $nilaiPenguji ? $nilaiPenguji->total_nilai : 0;
    }

    public static function getNoSKAndTahunAjaran($kode_prodi)
    {
        return DB::table('master_ta')
            ->join('no_sk', 'master_ta.ta', '=', 'no_sk.tahun_ajaran')
            ->select('no_sk.no_sk', 'master_ta.ta')
            ->where('master_ta.status', 1)
            ->where('no_sk.prodi', $kode_prodi)
            ->first();
    }
    public static function getNoSKAndTahunAjaranProdiID($kode_prodi)
    {
        return DB::table('master_ta')
            ->join('no_sk', 'master_ta.ta', '=', 'no_sk.tahun_ajaran')
            ->select('no_sk.no_sk', 'master_ta.ta')
            ->where('master_ta.status', 1)
            ->where('no_sk.prodi_ID', $kode_prodi)
            ->first();
    }
}
