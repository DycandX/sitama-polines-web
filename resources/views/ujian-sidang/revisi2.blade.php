@extends('layouts.app')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0">Revisi Ujian Sidang Akhir</h4>
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
                            <h5 class="card-title m-0">Detail Mahasiswa</h5>
                            <div class="card-tools">
                                <a href="{{ route('ujian-sidang.index') }}" class="btn btn-tool"><i class="fas fa-arrow-left"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($infoMhs)
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">NIM</p>
                                    </div>
                                    <div class="col">
                                        <p>: {{ $infoMhs->mhs_nim }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">Nama Mahasiswa</p>
                                    </div>
                                    <div class="col">
                                        <p>: {{ $infoMhs->mhs_nama }}</p>
                                    </div>
                                </div>
                                @if ($ta_mahasiswa->dosen)
                                    @foreach ($ta_mahasiswa->dosen as $pembimbing)
                                        <div class="row">
                                            <div class="col col-md-4">
                                                <p class="font-weight-bold">Dosen Pembimbing {{ $loop->iteration }}</p>
                                            </div>
                                            <div class="col">
                                                <p>: {{ $pembimbing['dosen_nama'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">Tahun Akademik</p>
                                    </div>
                                    <div class="col">
                                        <p>: {{ $infoMhs->tahun_akademik }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">Judul TA</p>
                                    </div>
                                    <div class="col">
                                        <p>: {{ $infoMhs->judul_final }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">Ruangan</p>
                                    </div>
                                    <div class="col">
                                        <p>: {{ $infoMhs->ruangan_nama }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">Sebagai</p>
                                    </div>
                                    <div class="col">
                                        <p> :@if (isset($infoMhs->dosen_nama))
                                                Penguji {{ $infoMhs->urutan }}
                                            @else
                                                Tidak diketahui
                                            @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold m-0">File Revisi</p>
                                    </div>

                                    <div class="col">
                                        <p class="m-0">:
                                            @if (isset($infoMhs->revisi_file))
                                                <a href="{{ asset('storage/draft_revisi/' . $infoMhs->revisi_file) }}" target="_blank"> {{ $infoMhs->revisi_file_original }}</a>
                                            @else
                                                Tidak Ada Revisi
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @else
                                <p class="text-danger">Data mahasiswa tidak ditemukan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });

        $(document).ready(function() {
            // Check if DataTable is already initialized
            if (!$.fn.DataTable.isDataTable('#datatable-main')) {
                $('#datatable-main').DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo('#datatable-main_wrapper .col-md-6:eq(0)');
            }
        });

        $('.confirm-nilai').click(function(event) {
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
