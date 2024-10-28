@extends('layouts.app')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection
<style>
    /* Atur lebar kolom tanggal */
    #datatable-coba th:nth-child(7),
    #datatable-coba td:nth-child(7) {
        width: 250px; /* Sesuaikan lebar sesuai kebutuhan */
    }

    /* Perkecil kolom status */
    #datatable-coba th:nth-child(8),
    #datatable-coba td:nth-child(8) {
        width: 30px; /* Sesuaikan lebar sesuai kebutuhan */
    }
</style>
@php
    use Carbon\Carbon;
@endphp

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
                            <form role="form" action="{{ route('bimbingan-dosen-magang.index')}}" method="get">
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
                        <table id="datatable-coba" class="table table-bordered table-striped">
                            <thead>
                            <th class="text-center">No</th>
                            <th>Tahun Ajaran</th>
                            <th>NIM</th>
                            <th>Mahasiswa</th>
                            <th>Pembimbing</th>
                            <th>Tempat Magang</th>
                            <th>Tanggal Magang</th>
                            <th>Status</th>
                            <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{$item->ta}}</td>
                                    <td>{{ $item->mhs_nim }}</td>
                                    <td>{{$item->mhs_nama}}</td>
                                    <td>{{ $item->dosen_nama }} ({{ $item->dosen_nip }})</td>
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
                                    <td> @if($item->validasi_id == null)
                                    <span class="badge badge-warning">Sedang Magang</span>
                                    @else
                                    <span class="badge badge-success">Selesai Magang</span>
                                    @endif</td>
                                   
                                    <td>
                                    @if($item->validasi_id == null)
                                    <button type="button" class="btn btn-block btn-sm btn-outline-info"
                                        data-toggle="dropdown"><i class="fas fa-eye"></i>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item" href="{{ route('bimbingan-dosen-magang.logbook', $item->magang_id) }}">LogBook</a>
                                        <a class="dropdown-item" href="{{ route('bimbingan-dosen-magang.bimbingan', $item->magang_id) }}">Bimbingan</a>
                                        <a class="dropdown-item" href="{{ route('bimbingan-dosen-magang.laporan', $item->magang_id) }}">File Magang</a>
                                        <a class="dropdown-item" href="{{ route('bimbingan-dosen-magang.validasi', $item->magang_id) }}">Validasi</a>
                                    </div>
                                    @else
                                    <button type="button" class="btn btn-block btn-sm btn-outline-info"
                                        data-toggle="dropdown"><i class="fas fa-eye"></i>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item" href="{{ route('bimbingan-dosen-magang.logbook', $item->magang_id) }}">LogBook</a>
                                        <a class="dropdown-item" href="{{ route('bimbingan-dosen-magang.bimbingan', $item->magang_id) }}">Bimbingan</a>
                                        <a class="dropdown-item" href="{{ route('bimbingan-dosen-magang.laporan', $item->magang_id) }}">File Magang</a>
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
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.container-fluid -->
</div>
@endsection

@push('js')
<script>
    // Ambil elemen select
    var selectElement = document.getElementById('ta');

    // Tambahkan event listener untuk perubahan nilai
    selectElement.addEventListener('change', function() {
        // Submit formulir saat opsi dipilih
        document.getElementById('filterForm').submit();
    });
</script>
@endpush

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
        if ($.fn.DataTable.isDataTable('#datatable-coba')) {
            $('#datatable-coba').DataTable().destroy();
        }

        // Initialize DataTable
        $('#datatable-coba').DataTable({
            "responsive": true,
            "lengthChange": true,
            "lengthMenu": [ 
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
        }).buttons().container().appendTo('#datatable-coba_wrapper .col-md-6:eq(0)');
    });
</script>
@endpush


