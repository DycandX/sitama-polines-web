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
                    <h4 class="m-0">Statistik Penguji Sidang Tugas Akhir</h4>
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
                            <form action="{{ route('ta.statistikPenguji') }}" method="GET">
                                <div class="row d-flex align-items-center">
                                    <div class="col-md-3 mb-md-0 mb-2">
                                        <select class="custom-select" name="tahun_akademik">
                                            <option value="">All Tahun Akademik</option>
                                            @for ($year = 2020; $year <= 2023; $year++)
                                                <option value="{{ $year . '/' . ($year + 1) }}"
                                                    {{ request('tahun_akademik') == $year . '/' . ($year + 1) ? 'selected' : '' }}>
                                                    {{ $year }}/{{ $year + 1 }}</option>
                                            @endfor
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
                                        <th scope="col">Dosen</th>
                                        <th scope="col">Jumlah Menguji</th>
                                        <th scope="col">Jumlah Sekretaris</th>
                                        <th scope="col">Jumlah Menguji + Sekretaris</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dosenList as $row)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $row->dosen_nama }}</td>
                                            <td>{{ $row->jml_menguji }}</td>
                                            <td>{{ $row->jml_sekretaris }}</td>
                                            <td>{{ $row->jml_menguji + $row->jml_sekretaris }}</td>
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
                    "pageLength": 50,
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
