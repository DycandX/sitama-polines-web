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


@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Jadwal Seminar Magang</h4>
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
                            <a href="{{ route('seminar.create') }}" class="btn btn-success"><i class="fas fa-plus-circle"></i> Tambah Jadwal
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-bordered table-striped">
                            <thead>
                                <th>No</th>
                                <th>Nama Mahasiswa</th>
                                <th>Tanggal Seminar</th>
                                <th>Ruangan</th>
                                <th>Waktu</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->mhs_nama }}</td>
                                    <td>{{ $item->tgl_seminar ? Carbon::parse($item->tgl_seminar)->format('d-m-Y') : '' }}</td>
                                    <td>{{ $item->ruangan_nama ?? '' }}</td>
                                    <td >{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') ?? '' }}</td>
                                    <td class="text-center">
                                        @if($item->status_seminar == 0)
                                        <form action="{{ route('seminar.valid', ['seminar_id' => $item->seminar_id]) }}" method="post">
                                            @csrf
                                            <input type="hidden" name="data-id" value="{{ $item->seminar_id }}">
                                            <button type="submit" class="btn btn-primary btn-sm">Verifikasi</button>
                                        </form>
                                        @else
                                        <span class="badge badge-success">Terlaksana</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <div class="dropdown-menu" role="menu">
                                                <a class="dropdown-item" href="{{ route('seminar.edit', $item->magang_id) }}">Edit</a>
                                                <form method="POST" action="{{ route('seminar.destroy', $item->magang_id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a class="dropdown-item confirm-button" href="#">Hapus</a>
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
        // Check if DataTable is already initialized
        if (!$.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable({
                "responsive": true,
                "lengthChange": true, // Mengaktifkan opsi "Show Entries"
                "lengthMenu": [ // Mengatur opsi jumlah entri yang ditampilkan
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "autoWidth": false,
            })
        }
    });
</script>
@endpush
