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
                    <h4 class="m-0">Daftar Bimbingan</h4>
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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <form action="{{ route('bimbingan.index') }}" method="GET">
                                <div class="row d-flex align-items-center">
                                    <div class="col-md-3 mb-md-0 mb-2">
                                        <select class="custom-select" name="tahun_akademik">
                                            <option value="">All Tahun Akademik</option>
                                            @for ($year = 2023; $year <= date('Y'); $year++)
                                                <option value="{{ $year . '/' . ($year + 1) }}"
                                                    {{ request('tahun_akademik') == $year . '/' . ($year + 1) || $year . '/' . ($year + 1) == $default_ta->ta ? 'selected' : '' }}>
                                                    {{ $year }}/{{ $year + 1 }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-md-0 mb-2">
                                        <select class="custom-select" name="kode_prodi">
                                            <option value="">All Program Studi</option>
                                            @foreach ($kode_prodi as $prodi)
                                                <option value="{{ $prodi->prodi_ID }}"
                                                    {{ request('kode_prodi') == $prodi->prodi_ID ? 'selected' : '' }}>
                                                    {{ $prodi->program_studi }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md mb-md-0 mb-2">
                                        <button type="submit" class="btn btn-sm btn-sm-block btn-primary">Filter</button>
                                    </div>
                                    <div class="card-tools">
                                        <a href="{{ route('bimbingan.create') }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-plus-circle"></i> Tambah Data Bimbingan
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <table id="datatable-bebas" class="table-bordered table-striped table text-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">NIM</th>
                                        <th scope="col">Mahasiswa</th>
                                        <th scope="col">Pembimbing</th>
                                        <th scope="col">Judul TA</th>
                                        <th scope="col">Tahun Akademik</th>
                                        <th scope="col">Syarat Sidang</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tm_collection as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->mhs_nim }}</td>
                                            <td>{{ $item->mhs_nama }}</td>
                                            <td>
                                                @if (isset($item->dosen_nama))
                                                    <div class="mb-2">
                                                        <ol class="m-0 px-2 pl-3">
                                                            @foreach ($item->dosen as $dosen)
                                                                <li class="m-0 p-0">{{ $dosen['dosen_nama'] }}
                                                                    <span class="badge badge-warning">
                                                                        {{ $log_bimbingan[$dosen['dosen_nip']][$item->mhs_nim]['jml_bimbingan_valid'] }}
                                                                        /
                                                                        {{ $log_bimbingan[$dosen['dosen_nip']][$item->mhs_nim]['jml_bimbingan'] }}
                                                                    </span>
                                                                    {!! $dosen['dosen_verifikasi'] == 1 ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>' !!}
                                                                </li>
                                                            @endforeach
                                                        </ol>
                                                    </div>
                                                    <a style="text-decoration: underline;"
                                                        href="{{ route('bimbingan.bimblog', $item->ta_id) }}"
                                                        class="">Lihat Catatan Bimbingan</a>
                                                @else
                                                    <span class="badge badge-danger">Belum Plotting</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->ta_judul }}</td>
                                            <td>{{ $item->tahun_akademik }}</td>
                                            <td class="text-center">
                                                <div class="mb-2">
                                                    @if (!isset($item->dosen_nama))
                                                        <span class="badge badge-danger">Belum Plotting Pembimbing</span>
                                                    @elseif ($item->nullSyarat)
                                                        <span class="badge badge-danger">Belum diupload</span>
                                                    @elseif ($item->jumlahVerif == $item->jumlahMasterSyarat)
                                                        <span class="badge badge-success">Diverifikasi</span>
                                                    @else
                                                        <span
                                                            class="badge {{ $item->jumlahSyarat == $item->jumlahMasterSyarat ? 'badge-warning' : 'badge-danger' }}">{{ $item->jumlahVerif . ' / ' . $item->jumlahSyarat }}</span>
                                                    @endif
                                                </div>
                                                <a href="{{ route('bimbingan.show', $item->ta_id) }}"
                                                    style="text-decoration: underline;">Cek Syarat Sidang</a>

                                            </td>
                                            <td class="text-nowrap">
                                                <a class="btn btn-sm btn-warning"
                                                    href="{{ route('bimbingan.edit', $item->ta_id) }}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form method="POST" style="display: inline-block"
                                                    action="{{ route('bimbingan.destroy', $item->ta_id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger confirm-button">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
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

    @push('js')
        <!-- DataTables & Plugins -->
        <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
        <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
        <script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

        <!-- Additional DataTables Initialization -->
        <script>
            $(document).ready(function() {
                // Check if DataTable is already initialized
                if (!$.fn.DataTable.isDataTable('#datatable-bebas')) {
                    $('#datatable-bebas').DataTable({
                        "responsive": true,
                        "lengthChange": false,
                        "autoWidth": false,
                        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
                    }).buttons().container().appendTo('#datatable-bebas_wrapper .col-md-6:eq(0)');
                }
            });
        </script>
    @endpush
@endsection
