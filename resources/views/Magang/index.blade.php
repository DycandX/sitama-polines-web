@extends('layouts.app')

@push('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush
@php
    use Carbon\Carbon;
@endphp
<style>
    /* Atur lebar kolom tanggal */
    #datatable-new th:nth-child(7),
    #datatable-new td:nth-child(7) {
        width: 160px; /* Sesuaikan lebar sesuai kebutuhan */
    }
    #datatable-new th:nth-child(8),
    #datatable-new td:nth-child(8) {
        width: 10px; /* Sesuaikan lebar sesuai kebutuhan */
    }
</style>

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Data Mahasiswa Magang</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                </ol>
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
                        <h5 class="m-0"></h5>
                        <div class="card-tools">
                            <form role="form" action="{{ route('data-magang.index')}}" method="get">
                            <div class="form-group input-group">
                                    <label class="font-weight-normal p-1">Tahun Ajaran:</label>
                                    <select class="form-control" id="ta" name="ta" >
                                        <option value="2023/2024"  selected > 2023/2024 </option>
                                        <option value="2022/2023" > 2022/2023 </option>
                                        <option value="2021/2022" > 2021/2022 </option>
                                        <option value="2020/2021" > 2020/2021 </option>
                                    </select>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="submit"><i class="fa fa-check"></i> Pilih</button>
                                    </span>									
                                </div>			
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable-new" class="table table-bordered table-striped">
                            <thead>
                                <th>No</th>
                                <th>Tahun Ajaran</th>
                                <th class="text-center">NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Dosen Pembimbing</th>
                                <th>Industri</th>
                                <th>Tanggal Magang</th>
                                <th>Nilai Akhir</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{$item->ta}}</td>
                                    <td>{{ $item->mhs_nim }}</td>
                                    <td>{{ $item->mhs_nama }}</td>
                                    <td>{{ $item->dosen_nama }}</td>
                                    <td>
                                        @for ($i = 0; $i < count($item->nama_industri); $i++)
                                            {{ $item->nama_industri[$i] }}<br>
                                        @endfor
                                    </td>
                                    <td>
                                    @for ($i = 0; $i < count($item->nama_industri); $i++)
                                        @php
                                            $tglMulai = isset($item->tgl_mulai[$i]) ? Carbon::parse($item->tgl_mulai[$i])->locale('id')->translatedFormat('d-m-Y') : '';
                                            $tglSelesai = isset($item->tgl_selesai[$i]) ? Carbon::parse($item->tgl_selesai[$i])->locale('id')->translatedFormat('d-m-Y') : '';
                                        @endphp

                                        {{ $tglMulai }} sd {{ $tglSelesai }}

                                        @if ($i < count($item->nama_industri) - 1)
                                            <br>
                                        @endif
                                    @endfor
                                    </td>
                                    <td class="text-center">{{ $item->nilai_akhir !== null ? \App\Helpers\NilaiHelper::formatNilai($item->nilai_akhir) : '-' }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" role="menu">
                                                <a class="dropdown-item" href="{{ route('magang.edit', $item->magang_id) }}"><i></i> Plotting Dosen</a>
                                                <a class="dropdown-item" href="{{ route('magang.syarat', $item->magang_id) }}"><i></i> Detail Persyaratan</a>
                                                <a class="dropdown-item" href="{{ route('magang.nilai', $item->magang_id) }}"><i></i> Penilaian</a>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.container-fluid -->
</div>
@endsection

@push('js')
<!-- DataTables  & Plugins -->
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
        var userName = "{{ Auth::user()->name }}";
        var selectedYear = $('#ta').val();
        if ($.fn.DataTable.isDataTable('#datatable-new')) {
            $('#datatable-new').DataTable().destroy();
        }

        // Initialize DataTable
        $('#datatable-new').DataTable({
            "responsive": true,
            "lengthChange": true, // Enable "Show Entries" option
            "lengthMenu": [ // Set options for number of entries displayed
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "autoWidth": false,
            "buttons": [
                        {
                            extend: 'excel',
                            text: 'Export Excel',
                            className: 'btn btn-info',
                            filename: 'Laporan Nilai Magang',
                            title: function() {
                            return 'Laporan Rekapitulasi Data Magang Tahun ' + selectedYear + ' - Dibuat oleh: ' + userName;
                            },
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 7] 
                            }
                        }
                    ]
        }).buttons().container().appendTo('#datatable-new_wrapper .col-md-6:eq(0)');
    });
</script>
@endpush

<script>
    function setMagangId(magang_id) {
        sessionStorage.setItem('magang_id', magang_id);
    }
</script>
