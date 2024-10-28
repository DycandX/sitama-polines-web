@extends('layouts.app')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <style>
        #datatable-bjir_filter input {
            animation-name: example;
            animation-duration: 1s;
            animation-iteration-count: infinite;
            width: 240px;
        }

        @keyframes example {
            0% {
                background: white;
            }

            15% {
                background: #ccffbb;
                border: 1px solid #339933;
                transform: scale(1.03);
            }

            100% {
                background: white;
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
                            <form action="{{ route('ujian-sidang.index') }}" method="GET">
                                @csrf
                                <div class="row d-flex align-items-center">
                                    <div class="col-md-3 mb-md-0 mb-2">
                                        <select class="custom-select" name="akademik">
                                            <option value="">All Tahun Akademik</option>
                                            @for ($year = 2020; $year <= 2025; $year++)
                                                <option value="{{ $year . '/' . ($year + 1) }}"
                                                    {{ request('akademik') == $year . '/' . ($year + 1) ? 'selected' : '' }}>
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
                            <div class="table-responsive">
                                <table id="datatable-bjir" class="table-bordered table-striped table-hover table text-sm">
                                    <thead>
                                        <tr class="text-center">
                                            <th scope="col">No</th>
                                            <th scope="col">Mahasiswa</th>
                                            <th scope="col">Sesi TA</th>
                                            <th scope="col">Pembimbing</th>
                                            <th scope="col">Penguji</th>
                                            <th scope="col">Sekretaris</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Status Kelulusan</th>
                                            <th scope="col">Nilai Akhir</th>
                                            <th scope="col">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ta_mahasiswa as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    {{ $item->mhs_nama }}
                                                    <br> <b>({{ $item->mhs_nim }})</b> <br>
                                                </td>
                                                <td>
                                                    @if ($item->emptyPenguji)
                                                        <span class="badge badge-danger">Belum diplot</span>
                                                    @else
                                                        <div>
                                                            <b>Tanggal:</b><br>
                                                            {{ $item->tgl_sidang }}<br>
                                                        </div>
                                                        <div>
                                                            <b>Hari dan tanggal:</b><br>
                                                            {{ $item->format_tanggal }}<br>
                                                        </div>
                                                        <div>
                                                            <b>Ruangan:</b><br>
                                                            {{ $item->ruangan_nama }}
                                                        </div>
                                                        <div>
                                                            <b>Waktu:</b><br>
                                                            {{ $item->sesi_nama }}<br>
                                                            {{ date('H:i', strtotime($item->waktu_mulai)) }} -
                                                            {{ date('H:i', strtotime($item->waktu_selesai)) }}<br>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <ol class="px-2">
                                                        @foreach ($item->user_dosen as $pembimbing)
                                                            @if ($pembimbing['user_dosen_nip'] == $userNip)
                                                                <li class="m-0 p-0">
                                                                    <b>{{ $pembimbing['user_dosen_nama'] }}</b>
                                                                </li>
                                                            @else
                                                                <li class="m-0 p-0">{{ $pembimbing['user_dosen_nama'] }}
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ol>
                                                </td>
                                                <td>
                                                    @if ($item->emptyPenguji)
                                                        <span class="badge badge-danger">Belum diplot</span>
                                                    @else
                                                        <ol class="px-2">
                                                            @foreach ($item->user_dosen_penguji as $penguji)
                                                                @if ($penguji['user_dosen_nip'] == $userNip)
                                                                    <li class="m-0 p-0">
                                                                        <b>{{ $penguji['user_dosen_nama'] }}</b>
                                                                    </li>
                                                                @else
                                                                    <li class="m-0 p-0">{{ $penguji['user_dosen_nama'] }}
                                                                    </li>
                                                                @endif
                                                            @endforeach

                                                        </ol>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!isset($item->sekretaris))
                                                        <span class="badge badge-danger">Belum diplot</span>
                                                    @else
                                                        {{ $item->sekretaris }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($item->nilai_akhir == null)
                                                        <span class="badge badge-warning">Sudah terjadwal</span>
                                                    @else
                                                        <span class="badge badge-success">Sudah terlaksana</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($item->status_lulus == 0 && $item->nilai_akhir == null)
                                                        <span class="badge badge-warning">Belum melaksanakan sidang</span>
                                                    @elseif ($item->status_lulus == 0 && $item->nilai_akhir != null)
                                                        <span class="badge badge-warning">Sudah melaksanakan sidang</span>
                                                    @elseif ($item->status_lulus == 1)
                                                        <h6><span class="badge badge-success">Lulus</span></h6>
                                                    @elseif ($item->status_lulus == 2)
                                                        <h6><span class="badge badge-warning">Lulus dengan revisi</span>
                                                        </h6>
                                                    @elseif ($item->status_lulus == 3)
                                                        <h6><span class="badge badge-danger">Tidak lulus</span></h6>
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
                                                    @if ($item->emptyPenguji)
                                                        <span class="badge badge-danger">Belum diplot</span>
                                                    @else
                                                        @if ($item->isPembimbing)
                                                            <div class="btn-group">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false">
                                                                    <i class="fas fa-cog"></i>
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a class="dropdown-item" target="_blank"
                                                                        href="{{ route('ujian-sidang.CetakSuratTugas', $item->ta_id) }}">
                                                                        <i class="fas fa-file-pdf"></i> Cetak Surat Tugas
                                                                    </a>
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('ujian-sidang.kelayakan', ['ta_id' => $item->ta_id]) }}"
                                                                        data-toggle="tooltip" data-placement="top"
                                                                        title="Input Kelayakan Ujian Sidang">
                                                                        <i class="fas fa-edit"></i> Input Nilai
                                                                    </a>
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('ujian-sidang.revisi', ['ta_id' => $item->ta_id]) }}"
                                                                        data-toggle="tooltip" data-placement="top"
                                                                        title="Lihat Revisi">
                                                                        <i class="fas fa-eye"></i> Lihat Revisi
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @elseif ($item->isPenguji)
                                                            <div class="btn-group">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false">
                                                                    <i class="fas fa-cog"></i>
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a class="dropdown-item" target="_blank"
                                                                        href="{{ route('ujian-sidang.CetakSuratTugas', $item->ta_id) }}">
                                                                        <i class="fas fa-file-pdf"></i> Cetak Surat Tugas
                                                                    </a>
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('ujian-sidang.penguji', ['ta_id' => $item->ta_id]) }}"
                                                                        data-toggle="tooltip" data-placement="top"
                                                                        title="Input Nilai Penguji">
                                                                        <i class="fas fa-edit"></i> Input Nilai
                                                                    </a>
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('ujian-sidang.revisi2', ['ta_id' => $item->ta_id]) }}"
                                                                        data-toggle="tooltip" data-placement="top"
                                                                        title="Lihat Revisi">
                                                                        <i class="fas fa-eye"></i> Lihat Revisi
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @elseif($item->isSekre)
                                                            <div class="dropdown">
                                                                <button
                                                                    class="btn btn-sm btn-outline-secondary rounded-circle btn-tooltip dropdown-toggle"
                                                                    type="button"
                                                                    id="dropdownMenuButton-{{ $item->ta_sidang_id }}"
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false"
                                                                    style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                                    <i class="fas fa-user-graduate"></i>
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <form method="POST"
                                                                        action="{{ route('updateOrInsertStatusLulus', $item->ta_sidang_id) }}">
                                                                        @csrf
                                                                        <input type="hidden" name="statusLulus"
                                                                            value="2">
                                                                        <button type="submit"
                                                                            class="confirm-status dropdown-item"
                                                                            href="#">
                                                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                                                            Lulus dengan Revisi
                                                                        </button>
                                                                    </form>
                                                                    <form method="POST"
                                                                        action="{{ route('updateOrInsertStatusLulus', $item->ta_sidang_id) }}">
                                                                        @csrf
                                                                        <input type="hidden" name="statusLulus"
                                                                            value="1">
                                                                        <button type="submit"
                                                                            class="confirm-status dropdown-item"
                                                                            href="#">
                                                                            <i class="fas fa-check-circle mr-1"></i>
                                                                            Lulus
                                                                        </button>
                                                                    </form>
                                                                    <form method="POST"
                                                                        action="{{ route('updateOrInsertStatusLulus', $item->ta_sidang_id) }}">
                                                                        @csrf
                                                                        <input type="hidden" name="statusLulus"
                                                                            value="3">
                                                                        <button type="submit"
                                                                            class="confirm-status dropdown-item"
                                                                            href="#">
                                                                            <i class="fas fa-times-circle mr-1"></i>
                                                                            Tidak Lulus
                                                                        </button>
                                                                    </form>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a target="_blank" class="dropdown-item"
                                                                        href="/ta/{{ $item->ta_sidang_id }}?back=/ujian-sidang">
                                                                        <i class="fas fa-file-pdf mr-1"></i>
                                                                        Detail Nilai
                                                                    </a>
                                                                    <a target="_blank" class="dropdown-item"
                                                                        href="{{ route('ujian-sidang.nilai-pembimbing', ['ta_sidang_id' => $item->ta_sidang_id]) }}">
                                                                        <i class="fas fa-file-pdf mr-1"></i>
                                                                        Cetak Nilai Pembimbing
                                                                    </a>
                                                                    <a target="_blank" class="dropdown-item"
                                                                        href="{{ route('ujian-sidang.nilai-penguji', ['ta_sidang_id' => $item->ta_sidang_id]) }}">
                                                                        <i class="fas fa-file-pdf mr-1"></i>
                                                                        Cetak Nilai Penguji & Rekap
                                                                    </a>
                                                                    <a target="_blank" class="dropdown-item"
                                                                        href="{{ route('ujian-sidang.berita-acara', ['ta_sidang_id' => $item->ta_sidang_id]) }}">
                                                                        <i class="fas fa-file-pdf mr-1"></i>
                                                                        Cetak Berita Acara/Laporan Hasil
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif
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
@endsection

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
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["excel", "pdf"]
                }).buttons().container().appendTo('#datatable-bjir_wrapper .col-md-6:eq(0)');
            }


            $('.confirm-status').click(function(event) {
                var form = $(this).closest("form");
                form.submit();
            });
        });
    </script>
@endpush
