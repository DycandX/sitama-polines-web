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

        .danger-animation {
            animation: mymove 1s infinite;
        }

        @keyframes mymove {
            0%,
            100% {
                background-color: inherit;
            }
            15% {
                background-color: #ffd6d6;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Data Mahasiswa Bimbingan Tugas Akhir</h4>
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
                            <form action="{{ route('mhsbimbingan.index') }}" method="GET">
                                @csrf
                                <div class="row d-flex align-items-center">
                                    <div class="col-md-3 mb-md-0 mb-2">
                                        <select class="custom-select" name="akademik">
                                            @for ($year = 2023; $year <= date('Y'); $year++)
                                                <option value="{{ $year . '/' . ($year + 1) }}"
                                                    {{ request('akademik') == $year . '/' . ($year + 1) || $year . '/' . ($year + 1) == $default_ta->ta ? 'selected' : '' }}>
                                                    {{ $year }}/{{ $year + 1 }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-md-0 mb-2">
                                        <select class="custom-select" name="prodi">
                                            <option value="">All Program Studi</option>
                                            @foreach ($kode_prodi as $prodi)
                                                <option value="{{ $prodi->prodi_ID }}"
                                                    {{ request('prodi') == $prodi->prodi_ID ? 'selected' : '' }}>
                                                    {{ $prodi->program_studi }}
                                                </option>
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
                            <div id="datatable-container">
                                <table id="datatable-bjir" class="table-bordered table-striped table-hover table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">NIM</th>
                                            <th class="text-center">Mahasiswa</th>
                                            <th class="text-center">Judul TA</th>
                                            <th class="text-center">Tahun Akademik</th>
                                            <th class="text-center">Sebagai</th>
                                            <th class="text-center">Persetujuan Sidang Akhir</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ta_mahasiswa as $item)
                                            <tr class="{{ $item->verified != 1 ? 'danger-animation' : '' }}">
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td class="text-center">{{ $item->mhs_nim }}</td>
                                                <td class="text-center">{{ $item->mhs_nama }}</td>
                                                <td class="text-center">{{ $item->ta_judul }}</td>
                                                <td class="text-center">{{ $item->tahun_akademik }}</td>
                                                <td class="text-center">{{ 'Pembimbing' . ' ' . $item->urutan }}</td>
                                                <td class="text-center">
                                                    @if ($item->verified == 1)
                                                        <span class="badge badge-success">Sudah Menyetujui</span>
                                                    @else
                                                        @if ($item->jml_bimbingan_valid < $masterJumlah)
                                                            <span class="badge badge-danger">
                                                                {{ $item->jml_bimbingan_valid }}/{{ $masterJumlah }}
                                                            </span>
                                                            @if ($item->jml_bimbingan_invalid != 0)
                                                                <br>
                                                                <span class="badge badge-danger">
                                                                    {{ $item->jml_bimbingan_invalid }} bimbingan<br>butuh
                                                                    validasi
                                                                </span>
                                                            @endif
                                                        @else
                                                            <form
                                                                action="{{ route('setujui.sidang.akhir', $item->ta_id) }}"
                                                                style="display: inline-block" method="POST">
                                                                @csrf
                                                                @if ($item->verified != 1)
                                                                    <input type="hidden" name="urutan"
                                                                        value="{{ $item->urutan }}">
                                                                    <button class="confirm-verif btn btn-danger">
                                                                        <i class="fa fa-check"></i>
                                                                        Setujui Untuk Sidang
                                                                    </button>
                                                                @endif
                                                            </form>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td class="text-center text-nowrap">
                                                    <a class="btn btn-primary my-tooltip top"
                                                        href="{{ route('mhsbimbingan.pembimbingan', $item->ta_id) }}">
                                                        <span class="tooltiptext">Lihat Aktivitas Bimbingan</span> </button>
                                                        <i class="fa fa-clipboard-list"></i></a>
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
@endsection

@push('js')
    <!-- DataTables  & Plugins -->
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

    <script>
        $(document).ready(function() {
            // Check if DataTable is already initialized
            if (!$.fn.DataTable.isDataTable('#datatable-bjir')) {
                $('#datatable-bjir').DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo('#datatable-bjir_wrapper .col-md-6:eq(0)');
            }
        });

        $('.confirm-verif').click(function(event) {
            var form = $(this).closest("form");
            event.preventDefault();
            swal({
                    title: `Yakin diverifikasi?`,
                    icon: "warning",
                    buttons: {
                        confirm: {
                            text: 'Ya'
                        },
                        cancel: 'Tidak'
                    },
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });
    </script>
@endpush
