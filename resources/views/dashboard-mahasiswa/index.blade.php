@extends('layouts.app')

@section('content')
    @if (!isset($dataTa))
        <div class="content">
            <div class="container-fluid">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-8" style="position: absolute; top: 50%; transform: translateY(-50%)">
                        <div class="card card-info card-outline text-center shadow">
                            <div class="card-header">
                                <h4 class="font-weight-bold m-0">Pendaftaran Tugas Akhir</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('dashboard-mahasiswa.store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    Judul Tugas Akhir <span class="text-danger text-bold">*</span>
                                    <input type="text" class="form-control mb-3 text-center" name="judul_ta"
                                        id="judul_ta" placeholder="Masukkan Judul Tugas Akhir (wajib diisi)">
                                    <hr>
                                    Nama Anggota Kelompok:
                                    <div class="input-group">
                                        <input type="text" name="tim-nama" class="form-control text-center"
                                            placeholder="Masukkan Nama Rekan Kelompok (tidak wajib diisi apabila individu)">
                                        <a href="javascript:;" id="tim-change"
                                            class="input-group-text bg-transparent">Ganti</a>
                                    </div>
                                    <input type="hidden" name="tim-id">
                                    <hr>
                                    <a class="btn btn-info confirm-upload" href="#">Ajukan Judul</a>
                                    {{-- <button type="submit" class="btn btn-info">Kirim</button> --}}
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4 class="m-0">Selamat datang {{ ucwords(auth()->user()->name) }}</h4>
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
                    <div class="col">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h5 class="m-0">Data Mahasiswa</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">Nama Mahasiswa</p>
                                    </div>
                                    <div class="col">
                                        <p>: {{ $mahasiswa->mhs_nama }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">NIM</p>
                                    </div>
                                    <div class="col">
                                        <p>: {{ $mahasiswa->mhs_nim }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">Prodi</p>
                                    </div>
                                    <div class="col">
                                        <p>: {{ $mahasiswa->program_studi }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">Tahun Akademik</p>
                                    </div>
                                    <div class="col">
                                        <p>: {{ $mahasiswa->tahun_akademik }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-4">
                                        <p class="font-weight-bold">Judul Tugas Akhir</p>
                                    </div>
                                    <div class="col">
                                        <p>: {{ $mahasiswa->ta_judul }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (isset($mahasiswa->dosen))
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h5 class="m-0">Data Pembimbing</h5>
                                </div>
                                <div class="card-body">
                                    @foreach ($mahasiswa->dosen as $pembimbing)
                                        <div class="row">
                                            <div class="col col-md-4">
                                                <p class="font-weight-bold">Nama Pembimbing {{ $loop->iteration }}</p>
                                            </div>
                                            <div class="col">
                                                <p>: {{ $pembimbing['dosen_nama'] }}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col col-md-4">
                                                <p class="font-weight-bold">NIP Pembimbing {{ $loop->iteration }}</p>
                                            </div>
                                            <div class="col">
                                                <p>: {{ $pembimbing['dosen_nip'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h5 class="m-0">Data Pembimbing</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning alert-dismissible">
                                        <h5 class="text-bold"><i class="icon fas fa-exclamation-triangle"></i> Data
                                            Pembimbing belum diplotting!</h5>
                                        Sedang proses ploting pembimbing
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
@endsection
@push('js')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <script>
        $(function() {
            $("#tim-change").click(function() {
                $("input[name=tim-id]").val("");
                $("input[name=tim-nama]").val("");
                $("input[name=tim-nama]").removeAttr("readonly");
                $("input[name=tim-nama]").focus();
            });
            $("input[name=tim-nama]").autocomplete({
                source: "/dashboard-mahasiswa/autocomplete",
                minLength: 2,
                select: function(event, ui) {
                    $("input[name=tim-id]").val(ui.item.id);
                    var nama = ui.item.value + " (" + ui.item.id + ")";
                    $("input[name=tim-nama]").val(nama);
                    $("input[name=tim-nama]").attr("readonly", "readonly");
                }
            });
        });
    </script>
    <script>
        $('.toast').toast('show');

        $('.confirm-upload').click(function(event) {
            var form = $(this).closest("form");
            event.preventDefault();
            swal({
                title: `Apakah Anda Yakin?`,
                icon: "warning",
                buttons: {
                    confirm: {
                        text: 'Ya'
                    },
                    cancel: 'Tidak'
                },
                dangerMode: true,
            }).then((willUpload) => {
                if (willUpload) {
                    form.submit();
                }
            });
        });

        $('form').keypress(function(event) {
            if (event.which == 13) {
                event.preventDefault();
                $('.confirm-upload').click();
            }
        });
    </script>
@endpush
