@extends('layouts.app')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Sidang Tugas Akhir</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right"></ol>
                </div>
            </div>
        </div>
    </div>

    @if (isset($taSidang))
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Data Sidang Tugas Akhir</h3>
                                <div class="card-tools">
                                    <!--
                                                    <a href="/sidang-tugas-akhir/surat-tugas" class="btn btn-sm btn-success my-tooltip top">
                                                        <i class="fa fa-download"></i>
                                                        <span class="tooltiptext">
                                                            Unduh Surat Tugas
                                                        </span>
                                                    </a>
                                                -->
                                </div>
                            </div>
                            <div class="card-body">
                                @foreach ($mahasiswa->dosen as $pembimbing)
                                    <div class="row">
                                        <div class="col col-md-4">
                                            <p class="font-weight-bold">Pembimbing {{ $loop->iteration }}</p>
                                        </div>
                                        <div class="col">
                                            <p>: {{ $pembimbing['dosen_nama'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                @if (isset($mahasiswa->penguji_nama))
                                    @foreach ($mahasiswa->penguji as $penguji)
                                        <div class="row">
                                            <div class="col col-md-4">
                                                <p class="font-weight-bold">Penguji {{ $loop->iteration }}</p>
                                            </div>
                                            <div class="col">
                                                <p>: {{ $penguji['penguji_nama'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row">
                                        <div class="col col-md-4">
                                            <p class="font-weight-bold">Penguji</p>
                                        </div>
                                        <div class="col">
                                            <p>: <span class="badge badge-danger">Belum diplot</span></p>
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">Sekretaris</p>
                                    </div>
                                    <div class="col">
                                        <p>:
                                            @if (isset($mahasiswa->sekre))
                                                {{ $mahasiswa->sekre }}
                                            @else
                                                <span class="badge badge-danger">Belum diplot</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">Tahun Akademik</p>
                                    </div>
                                    <div class="col">
                                        <p>: {{ $mahasiswa->tahun_akademik }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold m-0">Judul Tugas Akhir</p>
                                    </div>
                                    <div class="col">
                                        <p class="m-0">: {{ $mahasiswa->judul_final }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h5 class="m-0">Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">Hari & Tanggal</p>
                                    </div>
                                    <div class="col">
                                        <p>: {{ $tanggal_sidang }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">Ruangan</p>
                                    </div>
                                    <div class="col">
                                        <p>: {{ $mahasiswa->ruangan_nama }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">Sesi</p>
                                    </div>
                                    <div class="col">
                                        <p>:
                                            {{ $mahasiswa->sesi_nama }}
                                            ({{ $hari_sidang == 'Jumat' ? $mahasiswa->sesi_waktu_mulai_jumat : $mahasiswa->sesi_waktu_mulai }}-{{ $hari_sidang == 'Jumat' ? $mahasiswa->sesi_waktu_selesai_jumat : $mahasiswa->sesi_waktu_selesai_jumat }})
                                        </p>
                                    </div>
                                </div>
                                @php
                                    $nilaiAkhir = $taSidang->nilai_akhir;
                                    $nilaiHuruf = '';
                                    if ($nilaiAkhir >= 80 && $nilaiAkhir <= 100) {
                                        $nilaiHuruf = 'A';
                                    } elseif ($nilaiAkhir >= 75 && $nilaiAkhir <= 79) {
                                        $nilaiHuruf = 'AB';
                                    } elseif ($nilaiAkhir >= 70 && $nilaiAkhir <= 74) {
                                        $nilaiHuruf = 'B';
                                    } elseif ($nilaiAkhir >= 66 && $nilaiAkhir <= 69) {
                                        $nilaiHuruf = 'BC';
                                    } elseif ($nilaiAkhir >= 60 && $nilaiAkhir <= 65) {
                                        $nilaiHuruf = 'C';
                                    } elseif ($nilaiAkhir >= 40 && $nilaiAkhir <= 59) {
                                        $nilaiHuruf = 'D';
                                    } elseif ($nilaiAkhir >= 1 && $nilaiAkhir <= 39) {
                                        $nilaiHuruf = 'E';
                                    } else {
                                        $nilaiHuruf = '-';
                                    }
                                @endphp
                                <!-- <div class="row">
                                                    <div class="col col-md-4">
                                                        <p class="font-weight-bold">Nilai Akhir</p>
                                                    </div>
                                                    <div class="col">
                                                        <p>: {{ $taSidang->nilai_akhir }} ( {{ $nilaiHuruf }} )</p>
                                                    </div>
                                                </div> -->
                                @if ($taSidang->status_lulus > 0)
                                    
                                    @if ($taSidang->status_lulus == 0)
                                        <div class="row">
                                            <div class="col col-md-4">
                                                <p class="font-weight-bold m-0">Status Sidang</p>
                                            </div>

                                            <div class="col">
                                                <p class="m-0">:
                                                    <span class="badge badge-warning">Belum Melaksanakan Sidang</span>
                                                </p>
                                            </div>
                                        </div>
                                    @elseif ($taSidang->status_lulus == 1)
                                        <div class="row">
                                            <div class="col col-md-4">
                                                <p class="font-weight-bold m-0">Status Sidang</p>
                                            </div>

                                            <div class="col">
                                                <p class="m-0">:
                                                    <span class="badge badge-success">Telah Lulus</span>
                                                </p>
                                            </div>
                                        </div>
                                    @elseif ($taSidang->status_lulus == 2)
                                        <div class="row">
                                            <div class="col col-md-4">
                                                <p class="font-weight-bold">Status Sidang</p>
                                            </div>

                                            <div class="col">
                                                <h>:
                                                    <span class="badge badge-warning">Lulus dengan Revisi</span>
                                                </h>
                                            </div>
                                        </div>
                                        @if (isset($mahasiswa->revisi_file))
                                            <div class="row">
                                                <div class="col col-md-4">
                                                    <p class="font-weight-bold">File Lampiran </p>
                                                </div>

                                                <div class="col">
                                                    <p>:
                                                        <a href="{{ asset('storage/draft_revisi/' . $mahasiswa->revisi_file) }}"
                                                            target="_blank"> {{ $mahasiswa->revisi_file_original }}</a>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                        <form action="{{ route('sidang-tugas-akhir.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row d-md-flex align-items-center">
                                                <div class="col-sm-6 col-md-4">
                                                    <p class="m-sm-0 font-weight-bold mb-1">Unggah File Revisi</p>
                                                </div>
                                                <div class="col">
                                                    <div class="custom-file">
                                                        <input class="form-control" type="file" id="draft_revisi"
                                                            name="draft_revisi" accept="application/pdf">
                                                        <span class="text-danger">Format file : PDF(Max 2MB)</span>
                                                        @error('draft')
                                                            <div class="invalid-feedback" role="alert">
                                                                <span>{{ $message }}</span>
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col col-md-4"></div>
                                                <div class="col-sm-6 col-md-8">
                                                    <button type="submit" class="btn btn-info col-md-3"><i
                                                            class="fa fa-save mr-1"></i>Upload</button>
                                                </div>
                                            </div>
                                        </form>
                                    @elseif ($taSidang->status_lulus == 3)
                                        <div class="row">
                                            <div class="col col-md-4">
                                                <p class="font-weight-bold m-0">Status Sidang</p>
                                            </div>

                                            <div class="col">
                                                <p class="m-0">:
                                                    <span class="badge badge-danger">Tidak Lulus</span>
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col">
                                            <p>: Data Sidang Tidak Ditemukan</p>
                                        </div>
                                    @endif
                                    <div class="mt-3">
                                        <a href="{{ url('/upload-lembar-pengesahan') }}" class="btn btn-primary">Upload Lembar Pengesahan</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="card card-primary card-outline">
                            <div class="card-body">
                                <div class="alert alert-warning alert-dismissible">
                                    <h5 class="text-bold"><i class="icon fas fa-exclamation-triangle"></i> Anda belum
                                        mendaftar sidang!</h5>
                                    Silahkan daftar sidang di halaman Daftar Tugas Akhir
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
@push('js')
    <script>
        $('.toast').toast('show')
    </script>
@endpush
