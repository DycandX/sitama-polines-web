@extends('layouts.app')
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <style>
        @media (max-width: 767.98px) {
            .btn-sm-block {
                display: block;
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Bimbingan</h4>
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
                <div class="col">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title m-0">Detail Mahasiswa</h5>
                            <div class="card-tools">
                                <a href="{{ route('bimbingan.index') }}" class="btn btn-tool"><i class="fas fa-arrow-left"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col col-md-4">
                                    <p class="font-weight-bold">NIM</p>
                                </div>
                                <div class="col">
                                    <p>: {{ $ta_mahasiswa->mhs_nim }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-md-4">
                                    <p class="font-weight-bold">Nama Mahasiswa</p>
                                </div>
                                <div class="col">
                                    <p>: {{ $ta_mahasiswa->mhs_nama }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-md-4">
                                    <p class="font-weight-bold">Tahun Akademik</p>
                                </div>
                                <div class="col">
                                    <p>: {{ $ta_mahasiswa->tahun_akademik }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-md-4">
                                    <p class="font-weight-bold">Judul Tugas Akhir</p>
                                </div>
                                <div class="col">
                                    <p>: {{ $ta_mahasiswa->ta_judul }}</p>
                                </div>
                            </div>
                            @foreach ($ta_mahasiswa->dosen as $pembimbing)
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold {{ $loop->last ? 'm-0' : '' }}">Pembimbing {{ $loop->iteration }}</p>
                                    </div>
                                    <div class="col">
                                        <p class="{{ $loop->last ? 'm-0' : '' }}">: {{ $pembimbing['dosen_nama'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <form action="{{ route('bimbingan.bimblog', $ta_mahasiswa->ta_id) }}" method="GET">
                                @csrf
                                <div class="row d-flex align-items-center">
                                    <div class="col-md-4 mb-md-0 mb-2">
                                        <select class="custom-select" name="pembimbing">
                                            <option value="">All Pembimbing</option>
                                            @foreach ($ta_mahasiswa->dosen as $pembimbing)
                                                <option value="{{ $pembimbing['dosen_nip'] }}" @if (request('pembimbing') == $pembimbing['dosen_nip']) selected @endif>Pembimbing {{ $loop->iteration . ' - ' . $pembimbing['dosen_nama'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md mb-md-0 mb-2">
                                        <button type="submit" class="btn btn-sm btn-sm-block btn-primary">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col table-responsive">
                                    <table id="datatable-bimb" class="table-striped table-bordered table-hover table">
                                        <thead>
                                            <th>No</th>
                                            <th>Dosen Pembimbing</th>
                                            <th>Judul Bimbingan</th>
                                            <th>Deskripsi</th>
                                            <th>Tanggal</th>
                                            <th>File</th>
                                            <th>Status</th>
                                        </thead>
                                        <tbody>
                                            @foreach ($log as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->dosen_nama }}</td>
                                                    <td>{{ $item->bimb_judul }}</td>
                                                    <td>{{ $item->bimb_desc }}</td>
                                                    <td>{{ $item->bimb_tgl }}</td>
                                                    <td>
                                                        @if (isset($item->bimb_file))
                                                            <a href="{{ asset('storage/draft_ta/' . $item->bimb_file) }}" target="_blank">{{ $item->bimb_file_original }}</a>
                                                        @else
                                                            Tidak Ada Lampiran
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($item->bimb_status == 0)
                                                            <span class="badge badge-danger">Belum Diverifkasi</span>
                                                        @elseif ($item->bimb_status == 1)
                                                            <span class="badge badge-success">Diverifkasi</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title m-0">Jumlah Bimbingan</h5>
                        </div>
                        <div class="card-body">
                            @foreach ($ta_mahasiswa->dosen as $pembimbing)
                            <div class="row">
                                <div class="col col-md-4">
                                    <p class="font-weight-bold {{ $loop->last ? 'm-0' : '' }}">Pembimbing {{ $loop->iteration . ' - ' . $pembimbing['dosen_nama'] }}</p>
                                </div>
                                <div class="col">
                                    <p class="{{ $loop->last ? 'm-0' : '' }}">:
                                        {{ $logJumlah->where('urutan', $pembimbing['urutan'])->where('bimb_status', 1)->count() . '/' . $logJumlah->where('urutan', $pembimbing['urutan'])->count() }}
                                        @if ($logJumlah->where('urutan', $pembimbing['urutan'])->where('bimb_status', 1)->count() >= $masterJumlah)
                                            <span class="badge badge-success ml-1">Terpenuhi</span>
                                        @else
                                            <span class="badge badge-danger ml-1">Belum Terpenuhi</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $('.toast').toast('show')

        $(function() {
            $("#datatable-bimb").DataTable({
                "responsive": true,
                "searching": true,
                lengthMenu: [
                    [10, 20, -1],
                    [10, 20, 'All']
                ],
                pageLength: 10,
            });
        });
    </script>
@endpush
