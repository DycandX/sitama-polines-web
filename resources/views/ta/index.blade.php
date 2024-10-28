@php
use Carbon\Carbon;
@endphp
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
                    <h4 class="m-0">Data Ujian Sidang Tugas Akhir</h4>
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
                            <form action="{{ route('ta.index') }}" method="GET">
                                @csrf
                                <div class="row d-flex align-items-center">
                                    <div class="col-md-3 mb-md-0 mb-2">
                                        <select class="custom-select" name="tahun_akademik">
                                            <option value="">All Tahun Akademik</option>
                                            @for ($year = 2020; $year <= 2025; $year++)
                                                <option value="{{ $year . '/' . ($year + 1) }}" {{ request('tahun_akademik') == $year . '/' . ($year + 1) ? 'selected' : '' }}>{{ $year }}/{{ $year + 1 }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-md-0 mb-2">
                                        <select class="custom-select" name="kode_prodi">
                                            <option value="">All Program Studi</option>
                                            @foreach ($kode_prodi as $prodi)
                                                <option value="{{ $prodi->prodi_ID }}" {{ request('kode_prodi') == $prodi->prodi_ID ? 'selected' : '' }}>
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
                            <table id="datatable-bjir" class="table-bordered table-striped table text-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Mahasiswa</th>
                                        <th scope="col">Sesi TA</th>
                                        <th scope="col">Dosen Pembimbing</th>
                                        <th scope="col">Tim Penguji</th>
                                        <th scope="col">Nilai</th>
                                        <th scope="col">Status Sidang</th>
                                        <th scope="col">Status Kelulusan</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($taSidang as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <p><b>NIM:</b> <br> {{ $item->mhs_nim }}</p>
                                                <p><b>Nama:</b> <br> {{ $item->mhs_nama }}</p>
                                            </td>
                                            <td>
                                                <div>
                                                    <b>Hari dan tanggal:</b><br>
                                                    {{ $item->tgl_sidang }}<br>
                                                </div>
                                                <div>
                                                    <b>Waktu:</b><br>
                                                    @php
                                                        $tglSidang = Carbon::parse($item->tgl_sidang);
                                                        $isJumat = $tglSidang->isFriday();
                                                        $waktuMulai = $isJumat ? $item->sesi_waktu_mulai_jumat : $item->sesi_waktu_mulai;
                                                        $waktuSelesai = $isJumat ? $item->sesi_waktu_selesai_jumat : $item->sesi_waktu_selesai;
                                                    @endphp
                                                    {{ $item->sesi_nama }}<br>
                                                    {{ $waktuMulai }} - {{ $waktuSelesai }}<br>
                                                </div>
                                                <div>
                                                    <b>Ruangan:</b><br>
                                                    {{ $item->ruangan_nama }}
                                                </div>
                                            </td>
                                            <td>
                                                <b>Dosen Pembimbing:</b>
                                                <ol class="pl-3">
                                                    @foreach ($item->dosen as $pembimbing)
                                                        <li>{{ $pembimbing['dosen_nama'] }}</li>
                                                    @endforeach
                                                </ol>
                                            </td>
                                            <td>
                                                @if (isset($item->penguji_nama) or isset($item->sekre))
                                                    <b>Dosen Penguji:</b>
                                                    @if (isset($item->penguji_nama))
                                                        <ol class="pl-3">
                                                            @foreach ($item->penguji as $p)
                                                                <li>{{ $p['penguji_nama'] }}</li>
                                                            @endforeach
                                                        </ol>
                                                    @else
                                                        <br>
                                                        <span class="badge badge-danger">Belum Diplotting</span>
                                                        <br>
                                                    @endif
                                                    <br> <b>Sekretaris:</b> <br>
                                                    @if (isset($item->sekre))
                                                        {{ $item->sekre }}
                                                    @else
                                                        <span class="badge badge-danger">Belum Diplotting</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-danger">Belum Diplotting</span>
                                                @endif
                                            </td>
                                            <td class="{{ isset($item->nilai_akhir) ? '' : 'text-center' }}">
                                                @if (isset($item->nilai_akhir))
                                                    {{ $item->nilai_akhir }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->status == '1')
                                                    <span class="badge badge-warning">Sudah Terjadwal</span>
                                                @elseif ($item->status == '2')
                                                    <span class="badge badge-success">Sudah terlaksana</span>
                                                @else
                                                    <span class="badge badge-danger">Belum Dijadwalkan</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->status_lulus == '2')
                                                    <span class="badge badge-warning">Lulus dengan Revisi</span>
                                                @elseif ($item->status_lulus == '1')
                                                    <span class="badge badge-success">Lulus</span>
                                                @elseif ($item->status_lulus == '3')
                                                    <span class="badge badge-danger">Tidak Lulus</span>
                                                @else
                                                    <span class="">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                        style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a target="_blank" class="dropdown-item" href="{{ route('ta.CetakSuratTugasAdmin', $item->ta_id) }}">
                                                            <i class="fas fa-file-alt"></i> Cetak Surat Tugas
                                                        </a>
                                                        @if ($item->status != '2')
                                                            <a class="dropdown-item" href="{{ route('ta.editPenguji', $item->ta_sidang_id) }}">
                                                                <i class="fas fa-users-cog"></i> Plot Tim Penguji
                                                            </a>
                                                        @endif
                                                        <a class="dropdown-item" href="{{ route('ta.show', $item->ta_sidang_id) }}">
                                                            <i class="fas fa-file-alt"></i> Detail Nilai
                                                        </a>
                                                        @if ($item->status != '2')
                                                            <form method="POST" action="{{ route('ta.destroy', $item->ta_sidang_id) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item confirm-button">
                                                                    <i class="fas fa-trash"></i> Hapus
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                    @if ($item->status_lulus != '1')
                                                        
                                                    @endif
                                                </div>
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

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@push('js')
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
            if (!$.fn.DataTable.isDataTable('#datatable-bjir')) {
                $('#datatable-bjir').DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": [{
                        extend: 'excelHtml5',
                        text: 'Export Tabel ke Excel',
                        exportOptions: {
                            columns: [0, 1, 5] // Indices of the columns you want to export
                        }
                    }]
                }).buttons().container().appendTo('#datatable-bjir_wrapper .col-md-6:eq(0)');
            }
        });

        $('.confirm-status').click(function(event) {
            var form = $(this).closest("form");
            event.preventDefault();
            swal({
                    title: "Anda Yakin?",
                    icon: "warning",
                    buttons: {
                        confirm: {
                            text: 'Ya'
                        },
                        cancel: 'Tidak'
                    },
                    dangerMode: true,
                })
                .then((confirm) => {
                    if (confirm) {
                        form.submit();
                    }
                });
        });
    </script>
@endpush
