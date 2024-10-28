@extends('layouts.app')

@push('css')
    <style>
        #cardFooter {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Detail Persyaratan</h4>
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
                                <a href="{{ route('bimbingan.index') }}" class="btn btn-tool"><i
                                        class="fas fa-arrow-left"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col col-md-4">
                                    <p class="font-weight-bold">NIM</p>
                                </div>
                                <div class="col">
                                    <p>: {{ $ta_mahasiswa->mhs_nim }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-md-4">
                                    <p class="font-weight-bold">Nama Mahasiswa</p>
                                </div>
                                <div class="col">
                                    <p>: {{ $ta_mahasiswa->mhs_nama }}</p>
                                </div>
                            </div>
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
                            <div class="row">
                                <div class="col col-md-4">
                                    <p class="font-weight-bold">Tahun Akademik</p>
                                </div>
                                <div class="col">
                                    <p>: {{ $ta_mahasiswa->tahun_akademik }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-md-4">
                                    <p class="font-weight-bold">Judul TA</p>
                                </div>
                                <div class="col">
                                    <p>: {{ $ta_mahasiswa->ta_judul }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title m-0">
                                Syarat Ujian TA
                            </h5>
                        </div>
                        <form action="{{ route('bimbinganTa.verifyAll', $ta_mahasiswa->ta_id) }}" method="POST">
                            @csrf
                            <div class="card-body pb-0">
                                <div class="row">
                                    <div class="col table-responsive">
                                        <table class="table-bordered table-striped table-hover table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">No</th>
                                                    <th scope="col">Syarat</th>
                                                    <th scope="col">File</th>
                                                    <th scope="col">Status</th>
                                                    @if (!$verifiedAll && !$nullSyarat)
                                                        <th scope="col" class="text-center align-middle">
                                                            <input type="checkbox" name="" id="parentCheck"
                                                                style="cursor: pointer">
                                                        </th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($syarat as $s)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $s->dokumen_syarat }}</td>
                                                        <td class="text-center">
                                                            @if (!isset($s->dokumen_file))
                                                                <span class="badge badge-danger">Belum Upload</span>
                                                            @else
                                                                {{-- <a href="{{ asset('storage/syarat_ta/' . $s->dokumen_file) }}"
                                                                    target="_blank" class="btn btn-sm btn-success"><i
                                                                        class="fa fa-eye"></i></a> --}}
                                                                <a href="#" data-toggle="modal"
                                                                    data-target="#modal-{{ $s->syarat_sidang_id }}"
                                                                    class="btn btn-sm btn-success">
                                                                    <i class="fa fa-eye"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if (!isset($s->dokumen_file))
                                                                <span class="badge badge-danger">Belum Upload</span>
                                                            @elseif ($s->verified == 0)
                                                                <span class="badge badge-danger">Belum Validasi</span>
                                                            @elseif ($s->verified == 1)
                                                                <span class="badge badge-success">Valid</span>
                                                            @elseif ($s->verified == 2)
                                                                <span class="badge badge-danger">Dokumen Tidak Valid</span>
                                                            @else
                                                                <span class="badge badge-warning">Invalid value</span>
                                                            @endif
                                                        </td>
                                                        @if (!$verifiedAll && !$nullSyarat)
                                                            <td class="text-center align-middle">
                                                                @if (isset($s->dokumen_file) && $s->verified == 0)
                                                                    <input type="checkbox" name="syaratCheck[]"
                                                                        value="{{ $s->dokumen_id }}" class="child-check"
                                                                        style="cursor: pointer">
                                                                @endif
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right" id="cardFooter">
                                <button class="confirm-verif btn btn-info"><i class="fa fa-save mr-2"></i>Validasi Berkas
                                    Terpilih</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@foreach ($syarat as $s)
    <div class="modal fade" id="modal-{{ $s->syarat_sidang_id }}" tabindex="-1" role="dialog"
        aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">{{ $s->dokumen_syarat }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- <p>{{ $dst->dokumen_file_original }}</p> --}}
                    <embed
                        src="/stream-document/{{ encrypt(env('APP_FILE_SYARAT_TA_PATH') . $s->dokumen_file) . '?dl=0&filename=' . $s->dokumen_file_original }}"
                        type="application/pdf" width="100%" height="400px">
                    <hr>
                    <div class="text-right">
                        <button type="button" data-syarat-sidang-id="{{ $s->syarat_sidang_id }}"
                            class="btn btn-danger btn-sm validasi-modal-invalid"><i class="fa fa-check"></i> Dokumen
                            Tidak Valid</button>
                        <button type="button" data-syarat-sidang-id="{{ $s->syarat_sidang_id }}"
                            class="btn btn-success validasi-modal"><i class="fa fa-check"></i> Dokumen Valid</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach


@push('js')
    <script>
        $(document).ready(function() {
            $(".validasi-modal").click(function() {
                var id = $(this).data('syarat-sidang-id');

                $.ajax({
                        method: "GET",
                        url: "/syarat-sidang-verifikasi-single/" + id + "?valid=1",
                    })
                    .done(function(msg) {
                        alert("Berhasil melakukan validasi berkas.");
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    });
            });
        });
        $(".validasi-modal-invalid").click(function() {
            var id = $(this).data('syarat-sidang-id');

            $.ajax({
                    method: "GET",
                    url: "/syarat-sidang-verifikasi-single/" + id + "?valid=2",
                })
                .done(function(msg) {
                    alert("Berhasil melakukan validasi berkas.");
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                });
        });
        $('#parentCheck').on('click', function() {
            if ($(this).prop('checked') === true) {
                $('.child-check').prop('checked', true);
                $('#cardFooter').show();
            } else {
                $('.child-check').prop('checked', false);
                $('#cardFooter').hide();
            }
        });

        $('.child-check').on('change', function() {
            if ($('.child-check:checked').length > 0) {
                $('#cardFooter').show();
            } else {
                $('#cardFooter').hide();
            }
        })

        $('.toast').toast('show')

        $('.confirm-verif').click(function(event) {
            var form = $(this).closest("form");
            event.preventDefault();
            swal({
                    title: 'Anda yakin ingin melakukan validasi?',
                    icon: "success",
                    buttons: {
                        confirm: {
                            text: 'Ya'
                        },
                        cancel: 'Tidak'
                    },
                    dangerMode: false,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });
    </script>
@endpush
