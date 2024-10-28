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
                    <h4 class="m-0">Pembimbingan</h4>
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
                            <form action="{{ route('bimbingan-mahasiswa.index') }}" method="GET">
                                @csrf
                                <div class="row d-flex align-items-center">
                                    <div class="col-md mb-md-0 mb-2">
                                        <select class="custom-select" name="pembimbing">
                                            <option value="">All Pembimbing</option>
                                            @foreach ($mahasiswa->dosen as $pembimbing)
                                                <option value="{{ $pembimbing['dosen_nip'] }}"
                                                    @if (request('pembimbing') == $pembimbing['dosen_nip']) selected @endif>Pembimbing
                                                    {{ $loop->iteration . ' - ' . $pembimbing['dosen_nama'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-md-0 mb-2">
                                        <select class="custom-select" name="verifikasi">
                                            <option value="">All Verifikasi</option>
                                            <option value="0" {{ request('verifikasi') == '0' ? 'selected' : '' }}>
                                                Belum Diverifikasi</option>
                                            <option value="1" {{ request('verifikasi') == '1' ? 'selected' : '' }}>
                                                Sudah Diverifikasi</option>
                                        </select>
                                    </div>
                                    <div class="col-md mb-md-0 mb-2">
                                        <button type="submit" class="btn btn-sm btn-sm-block btn-primary">Filter</button>
                                    </div>
                                    <div class="col text-right">
                                        <div class="card-tools">
                                            <a href="{{ route('bimbingan-mahasiswa.create') }}"
                                                class="btn btn-sm btn-success">
                                                <i class="fas fa-plus-circle"></i>
                                                Tambah Bimbingan
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col table-responsive">
                                    <table id="datatable-mhsbimb" class="table-striped table-bordered table-hover table">
                                        <thead>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Dosen Pembimbing</th>
                                            <th>Judul Bimbingan</th>
                                            <th>File</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </thead>
                                        <tbody>
                                            @foreach ($logCollect as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->format_tanggal }}</td>
                                                    <td>{{ $item->dosen_nama }}</td>
                                                    <td>{{ $item->bimb_judul }}</td>
                                                    <td class="text-center">
                                                        @if (isset($item->bimb_file))
                                                            <a href="/stream-document/{{ encrypt(env('APP_FILE_DRAFT_TA_PATH') . $item->bimb_file) . '?dl=0&filename=' . $item->bimb_file_original }}"
                                                                target="_blank"
                                                                class="btn btn-sm btn-primary my-tooltip top">
                                                                <i class="fa fa-eye"></i>
                                                                <span class="tooltiptext">
                                                                    Tampilkan File Bimbingan
                                                                </span>
                                                            </a>
                                                        @else
                                                            <div class="text-center">
                                                                -
                                                            </div>
                                                        @endif

                                                    </td>
                                                    <td>
                                                        @if ($item->bimb_status == 0)
                                                            <span class="badge badge-danger">Belum Diverifikasi</span>
                                                        @elseif ($item->bimb_status == 1)
                                                            <span class="badge badge-success">Diverifikasi</span>
                                                        @else
                                                            Invalid status
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($item->bimb_status == 1)
                                                            <a class="btn btn-sm btn-primary my-tooltip top"
                                                                href="{{ route('bimbingan-mahasiswa.show', $item->bimbingan_log_id) }}">
                                                                <i class="fas fa-eye"></i>
                                                                <span class="tooltiptext">
                                                                    Detail Bimbingan
                                                                </span>
                                                            </a>
                                                        @else
                                                            <button type="button"
                                                                class="btn btn-block btn-sm btn-outline-info"
                                                                data-toggle="dropdown"><i class="fas fa-cog"></i>
                                                            </button>
                                                            <div class="dropdown-menu" role="menu">
                                                                <a class="dropdown-item text-warning"
                                                                    href="{{ route('bimbingan-mahasiswa.edit', $item->bimbingan_log_id) }}">
                                                                    <i class="fas fa-edit text-warning mr-2"></i>Edit</a>
                                                                <div class="dropdown-divider"></div>
                                                                <form method="POST"
                                                                    action="{{ route('bimbingan-mahasiswa.destroy', $item->bimbingan_log_id) }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <a class="dropdown-item confirm-button text-danger"
                                                                        href="#">
                                                                        <i
                                                                            class="fas fa-trash-alt text-danger mr-2"></i>Hapus</a>
                                                                </form>
                                                            </div>
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
                            @php
                                $verified_all = 1;
                            @endphp
                            @foreach ($mahasiswa->dosen as $pembimbing)
                                @if ($pembimbing['verified'] == 1)
                                @else
                                    @php
                                        $verified_all = 0;
                                    @endphp
                                @endif
                            @endforeach
                            @if ($verified_all == 1)
                                <div class="card-tools float-right">
                                    <a href="/bimbingan-mahasiswa/cetak-persetujuan-sidang" class="btn btn-sm btn-danger"
                                        target="_blank">
                                        <i class="fa fa-file-pdf mr-1"></i> Cetak Persetujuan Sidang
                                    </a>
                                </div>
                            @endif
                            <h5 class="m-0">Status Bimbingan</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Urutan Pembimbing</th>
                                        <th>Nama</th>
                                        <th>Jumlah Bimbingan</th>
                                        <th>Lembar Kontrol</th>
                                        <th>Persetujuan Pendaftaran Sidang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $verified_all = 1;
                                    @endphp
                                    @foreach ($mahasiswa->dosen as $pembimbing)
                                        <tr>
                                            <td>{!! $pembimbing['urutan'] .
                                                ($pembimbing['urutan'] == 1 ? " <span class='ml-2 badge badge-success'>Utama</span>" : '') !!}
                                            </td>
                                            <td>{{ $pembimbing['dosen_nama'] }}</td>
                                            <td>
                                                {{ $logCollectJumlah->where('urutan', $pembimbing['urutan'])->where('bimb_status', 1)->count() .'/' .$logCollectJumlah->where('urutan', $pembimbing['urutan'])->count() }}
                                                @if ($logCollectJumlah->where('urutan', $pembimbing['urutan'])->where('bimb_status', 1)->count() >= $masterJumlah)
                                                    <span class="badge badge-success ml-1">Terpenuhi</span>
                                                @else
                                                    <span class="badge badge-danger ml-1">Belum Terpenuhi</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($logCollectJumlah->where('urutan', $pembimbing['urutan'])->where('bimb_status', 1)->count() >= $masterJumlah)
                                                    <a target="_blank"
                                                        href="{{ route('bimbingan-mahasiswa.CetakLembarKontrol', ['id' => $item->ta_id, 'sebagai' => $pembimbing['urutan']]) }}"
                                                        class="btn btn-sm btn-danger text-white"
                                                        style="text-decoration: none; color: inherit;">
                                                        <i class="fas fa-file-pdf mr-1"></i> Cetak Lembar Kontrol
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($pembimbing['verified'] == 1)
                                                    <span class="badge badge-success ml-1">Telah Menyetujui Pendaftaran
                                                        Sidang</span>
                                                @else
                                                    @php
                                                        $verified_all = 0;
                                                    @endphp
                                                    <span class="badge badge-danger ml-1">Belum Menyetujui Pendaftaran
                                                        Sidang</span>
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
@endsection
@push('js')
    <script>
        $('.toast').toast('show')

        $(function() {
            $("#datatable-mhsbimb").DataTable({
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
