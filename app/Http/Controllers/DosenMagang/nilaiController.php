<?php

namespace App\Http\Controllers\DosenMagang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Penilian;
use App\Models\Magang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class nilaiController extends Controller
{
    public function index()
    {
        $total = Penilian::nilai_akhirmhs();
        return view('nilai.penilaian', compact( 'total'));
    }

    public function nilaidosen($magang_id)
    {
        session(['magang_id' => $magang_id]);
        $menus = Menu::all();
        $nilai = Penilian::nilai_mhs();
        $komponen = Penilian::komponen_nilai();
        $coba = Penilian::nilaiperkomponen();
        $total = Penilian::nilai_akhirmhs();
        $validasi = Magang::validasimagangg($magang_id);

        return view('nilai.dosen', compact('menus','nilai','komponen','validasi','coba'));
    }
    public function nilaiindustri($magang_id)
    {
        $menus = Menu::all();
        $nilai = Penilian::nilaindustri($magang_id);
        return view('nilai.industri', compact('menus', 'nilai'));
    }
    public function store(Request $request)
    {

        $magang_id = $request->input('id'); 

        foreach ($request->nilai as $komponen_detail_id => $nilai) {
            \DB::table('penilians')->updateOrInsert(
                [
                    'magang_id' => $magang_id,
                    'komponen_detail_id' => $komponen_detail_id,
                ],
                [
                    'nilai' => $nilai
                ]
            );
        }
        

        $nilaiKomponen = \DB::table('penilians')
        ->where('penilians.magang_id', $magang_id)
        ->join('komponen_penilian_detail', 'komponen_penilian_detail.komponen_detail_id', '=', 'penilians.komponen_detail_id')
        ->join('komponen_penilian', 'komponen_penilian.komponen_id', '=', 'komponen_penilian_detail.komponen_id')
        ->select('penilians.nilai', 'komponen_penilian.bobot_komponen')
        ->get();

        $hitung = [];
        $totalNilai = 0;
        foreach ($nilaiKomponen as $item) {
            $hitung[$item->bobot_komponen][] = $item->nilai;
        }
    
        foreach ($hitung as $key=>$item)
        {
            $totalNilai += array_sum($item)/count($item)*$key/100;
        }
        $nilaidosenn = $totalNilai * 10;


        \DB::table('seminar_magangs')->where('magang_id', $magang_id)->update(['nilai_dosen' => $nilaidosenn]);

        $seminarMagang = \DB::table('seminar_magangs')->where('magang_id', $magang_id)->first();

        if ($seminarMagang) {
            $nilai_industri = $seminarMagang->nilai_industri ?? 0; 

            $nilai_akhir = ($nilaidosenn + $nilai_industri) / 2;

            \DB::table('seminar_magangs')->where('magang_id', $magang_id)->update(['nilai_akhir' => $nilai_akhir]);

    
            return redirect()->route('nilai-dosen-magang.index')->with('success', 'Nilai berhasil disimpan.');
        }
    }

    public function update(Request $request)
    {
        Log::info('Request Data: ', $request->all());

        
        $request->validate([
            'nilai.*' => 'required|integer|min:1|max:100',
            'magang_id.*' => 'required|integer'
        ]);

        $nilaiList = $request->input('nilai');
        $magangIdList = $request->input('magang_id');
        $totaltambah = 0;

        foreach ($nilaiList as $magang_industri_id => $nilai) {
            $magang_id = $magangIdList[$magang_industri_id];
            $totaltambah += $nilai;

            DB::table('magang_industris')
                ->where('magang_industri_id', $magang_industri_id)
                ->update(['nilai' => $nilai, 'magang_id' => $magang_id]);
        }
    
        if ($totaltambah > 0) {
            $countIndustri = count($nilaiList);
    
            $nilaiRataRata = $totaltambah / $countIndustri;
    
            \DB::table('seminar_magangs')->where('magang_id', $magang_id)->update(['nilai_industri' => $nilaiRataRata]);
        }

        $seminarMagang = \DB::table('seminar_magangs')->where('magang_id', $magang_id)->first();
        
        if ($seminarMagang) {
            $nilai_dosen = $seminarMagang->nilai_dosen ?? 0; 

            $nilai_akhir = ($nilai_dosen + $nilaiRataRata) / 2;

            \DB::table('seminar_magangs')->where('magang_id', $magang_id)->update(['nilai_akhir' => $nilai_akhir]);

    
            return redirect()->route('nilai-dosen-magang.index')->with('success', 'Nilai berhasil disimpan.');
        }

    
    }


}

?>