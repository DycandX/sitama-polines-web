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
                    <h4 class="m-0">Input Nilai Ujian Sidang Akhir</h4>
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
                                <a href="{{ route('ujian-sidang.index') }}" class="btn btn-tool"><i
                                        class="fas fa-arrow-left"></i></a>
                            </div>
                        </div>
                        <form action="{{ route('ujian-sidang.storeKelayakan', $infoMhs->ta_sidang_id) }}" method="POST">
                            @csrf
                            <div class="card-body">
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
                                <div class="row mb-3">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">Sebagai</p>
                                    </div>
                                    <div class="col">
                                        <p> :@if (isset($infoMhs->dosen_nama))
                                                Pembimbing {{ $infoMhs->urutan }}
                                            @else
                                                Tidak diketahui
                                            @endif
                                    </div>
                                </div>
                                @foreach ($nilai_pembimbing as $nilai)
                                    <input type="hidden" value="{{ $nilai->nilai_id }}" name="nilaiId[]">
                                    <div class="row mb-3">
                                        <div class="col col-md-4">
                                            <p class="font-weight-bold">{{ $nilai->unsur_nilai }}</p>
                                        </div>
                                        <div class="col">
                                            <select name="unsur[{{ $nilai->nilai_id }}]"
                                                class="form-control js-example-basic-single col-2">
                                                @for ($i = 100; $i >= 1; $i--)
                                                    <option
                                                        value="{{ $i }}"{{ isset($nilai_pembimbing_saved[$nilai->nilai_id]) && $i == $nilai_pembimbing_saved[$nilai->nilai_id] ? " selected='selected'" : '' }}>
                                                        {{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-info confirm-nilai mr-2"><i class="fa fa-save"></i>
                                    Simpan</button>
                                <a href="{{ route('ujian-sidang.index') }}" class="btn btn-danger"><i
                                        class="fas fa-times"></i>
                                    Batal</a>
                            </div>
                        </form>
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
