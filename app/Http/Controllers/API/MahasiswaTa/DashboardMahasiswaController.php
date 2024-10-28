<?php

namespace App\Http\Controllers\API\MahasiswaTa;

use App\Http\Controllers\Controller;
use App\Models\Bimbingan;
use App\Models\mahasiswa;
use App\Models\Ta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardMahasiswaController extends Controller
{
    public function index()
    {
        $id = Auth::user()->id;
        $dataTa = Ta::dataTa($id);
        $mahasiswa = Bimbingan::Mahasiswa($id);

        return response()->json([
            'dataTa' => $dataTa,
            'mahasiswa' => $mahasiswa
        ]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'judul_ta' => 'required'
        ]);

        if ($validator->fails()) {
            toastr()->error('Judul Tugas Akhir gagal ditambah </br> Periksa kembali data anda');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $id = Auth::user()->id;
        $nim = Bimbingan::Mahasiswa($id)->mhs_nim;
        $dataTa = Ta::dataTa($id);

        $thnAkademik = (date("Y") - 1) . "/" . date("Y");

        try {
            if (!isset($dataTa)) {
                $insert = new Ta();
                $insert->mhs_nim = $nim;
                $insert->ta_judul = $request->judul_ta;
                $insert->tahun_akademik = $thnAkademik;
                $insert->save();

                $ta_id = $insert->ta_id;

                DB::table('tas_mahasiswa')->insert([
                    "ta_id" => $ta_id,
                    "mhs_nim" => $nim
                ]);

                if ($request->post('tim-id')) {
                    $insert = new Ta();
                    $insert->mhs_nim = $request->post('tim-id');
                    $insert->ta_judul = $request->judul_ta;
                    $insert->tahun_akademik = $thnAkademik;
                    $insert->save();
                    DB::table('tas_mahasiswa')->insert([
                        "ta_id" => $ta_id,
                        "mhs_nim" => $request->post('tim-id')
                    ]);
                }

                toastr()->success('Judul Tugas Akhir berhasil disimpan');
            } else {
                toastr()->warning('Judul Tugas Akhir sudah ada');
            }
            return redirect('/dashboard-mahasiswa');
        } catch (\Throwable $th) {
            toastr()->warning('Terdapat masalah diserver');
            return redirect('/dashboard-mahasiswa');
        }
    }
    public function autocomplete()
    {
        $term = request()->get("term");
        $results = mahasiswa::where("mhs_nama", 'like', "%" . $term . "%")->get();

        $temp = [];
        foreach ($results as $row) {
            $temp[] = [
                "id" => $row->mhs_nim,
                "value" => $row->mhs_nama,
            ];
        }

        echo json_encode($temp);
    }
}
