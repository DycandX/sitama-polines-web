@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Detail Nilai</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right"></ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!-- Card for General Information -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title m-0">Data Mahasiswa</h5>
                            <div class="card-tools">
                                <a href="{{ $back }}" class="btn-tool"><i class="fas fa-arrow-left"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- General Information -->
                            @foreach ([
            'NIM' => $penilaian->mhs_nim,
            'Nama Mahasiswa' => $penilaian->mhs_nama,
            'Tahun Akademik' => $penilaian->tahun_akademik,
            'Nilai Akhir' => $penilaian->nilai_akhir,
        ] as $label => $value)
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="font-weight-bold">{{ $label }}</p>
                                    </div>
                                    <div class="col">
                                        <p>: {{ $value }}</p>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Status Kelulusan -->
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="font-weight-bold">Status Kelulusan</p>
                                </div>
                                <div class="col">
                                    <p>:
                                        @if ($penilaian->status_lulus == '2')
                                            <span class="badge badge-warning">Lulus dengan Revisi</span>
                                        @elseif ($penilaian->status_lulus == '1')
                                            <span class="badge badge-success">Lulus</span>
                                        @elseif ($penilaian->status_lulus == '0')
                                            <span class="badge badge-danger">Tidak Lulus</span>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card for Pembimbing Details -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="m-0">Detail Penilaian Pembimbing</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Pembimbing TA</th>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Unsur Penilaian</th>
                                            <th scope="col">Nilai</th>
                                            <th scope="col">Bobot</th>
                                            <th scope="col">Nilai Total <br> (Nilai * Bobot)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $counter = null;
                                            $jml_nilai = 0;
                                            $count_nilai = 0;
                                        @endphp
                                        @foreach ($pembimbing as $item)
                                            <tr>
                                                <td>{{ $item->dosen_nip != $counter ? $item->urutan : '' }}</td>
                                                <td>{{ $item->dosen_nip != $counter ? $item->dosen_nama : '' }}</td>
                                                <td>{{ $item->unsur_nilai }}</td>
                                                <td>{{ $item->berinilai }}</td>
                                                <td>{{ $item->bobot }}</td>
                                                <td>{{ $item->total }}</td>
                                            </tr>
                                            @php
                                                $jml_nilai += $item->total;
                                                $counter = $item->dosen_nip;
                                                $count_nilai++;

                                                if ($count_nilai == $unsur_pembimbing) {
                                                    echo '<tr>';
                                                    echo "<td colspan='5' class='text-right text-bold'>Nilai Pembimbing " .
                                                        $item->urutan .
                                                        '</td>';
                                                    echo "<td class='text-bold'>" . $jml_nilai . '</td>';
                                                    echo '</tr>';
                                                    $count_nilai = 0;
                                                    $jml_nilai = 0;
                                                }
                                            @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="nilai-pembimbing">
                                    <b>Rata-rata Nilai Pembimbing: {{ $penilaian->nilai_pembimbing }}</b>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card for Penguji Details -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="m-0">Detail Penilaian Penguji</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Penguji Sidang</th>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Unsur Penilaian</th>
                                            <th scope="col">Nilai</th>
                                            <th scope="col">Bobot</th>
                                            <th scope="col">Nilai Total <br> (Nilai * Bobot)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $counter = null;
                                            $jml_nilai = 0;
                                            $count_nilai = 0;
                                        @endphp
                                        @foreach ($penguji as $item)
                                            <tr>
                                                <td>{{ $item->dosen_nip != $counter ? $item->urutan : '' }}</td>
                                                <td>{{ $item->dosen_nip != $counter ? $item->dosen_nama : '' }}</td>
                                                <td>{{ $item->unsur_nilai }}</td>
                                                <td>{{ $item->berinilai }}</td>
                                                <td>{{ $item->bobot }}</td>
                                                <td>{{ $item->total }}</td>
                                            </tr>
                                            @php
                                                $count_nilai++;
                                                $jml_nilai += $item->total;
                                                $counter = $item->dosen_nip;
                                                if ($count_nilai == $unsur_penguji) {
                                                    echo '<tr>';
                                                    echo "<td colspan='5' class='text-right text-bold'>Nilai Penguji " .
                                                        $item->urutan .
                                                        '</td>';
                                                    echo "<td class='text-bold'>" . $jml_nilai . '</td>';
                                                    echo '</tr>';
                                                    $count_nilai = 0;
                                                    $jml_nilai = 0;
                                                }
                                            @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="nilai-penguji">
                                    <b>Rata-rata Nilai Penguji: {{ $penilaian->nilai_penguji }}</b>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('js/verification.js') }}"></script>
@endpush

<style>
    .nilai-pembimbing {
        text-align: right;
    }

    .nilai-penguji {
        text-align: right;
    }
</style>
