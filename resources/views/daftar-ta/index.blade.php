@extends('layouts.app')
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Daftar Tugas Akhir</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right"></ol>
                </div>
            </div>
        </div>
    </div>
    @if (isset($taSidang))
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="card card-primary card-outline">
                            <div class="card-body">
                                <div class="alert alert-success alert-dismissible">
                                    <h5 class="text-bold"><i class="icon fas fa-check"></i> Anda Sudah Mendaftar Sidang</h5>
                                    Silahkan masuk ke halaman Sidang Tugas Akhir
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        @if ($memenuhiBimbingan)
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h5 class="m-0">Persyaratan Tugas Akhir</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col table-responsive">
                                            <table class="table-bordered table-striped table-hover table">
                                                <thead>
                                                    <th>No</th>
                                                    <th>Syarat</th>
                                                    <th>File</th>
                                                    <th>Status</th>
                                                    <th width='120'>Unggah Syarat</th>
                                                </thead>
                                                <tbody>
                                                    @foreach ($dokumenSyaratTa as $dst)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $dst->dokumen_syarat }}</td>
                                                            <td class="text-center">
                                                                @if (isset($dst->dokumen_file))
                                                                    {{-- <a href="{{ asset('storage/syarat_ta/' . $dst->dokumen_file) }}" target="_blank">{{ $dst->dokumen_file_original }}</a> --}}
                                                                    <a href="#" data-toggle="modal"
                                                                        data-target="#modal-{{ $dst->dokumen_id }}"
                                                                        class="btn btn-sm btn-success">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                @else
                                                                    <span class="badge badge-warning">Belum Upload</span>
                                                                @endif

                                                            </td>
                                                            <td class="text-center">
                                                                @if ($dst->verified == 0 && !isset($dst->dokumen_file))
                                                                    <span class="badge badge-warning">Belum Upload</span>
                                                                @elseif ($dst->verified == 0)
                                                                    <span class="badge badge-danger">Menunggu
                                                                        Validasi</span>
                                                                @elseif ($dst->verified == 1)
                                                                    <span class="badge badge-success">Dokumen Valid</span>
                                                                @elseif ($dst->verified == 2)
                                                                    <span class="badge badge-danger">Dokumen Tidak
                                                                        Valid</span>
                                                                @else
                                                                    <span class="badge badge-warning">Invalid Value</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if (!isset($dst->dokumen_file) || $dst->verified == 0 || $dst->verified == 2)
                                                                    <form method="POST" action="/daftar-tugas-akhir/upload"
                                                                        enctype="multipart/form-data">
                                                                        @csrf
                                                                        <input type="file" name="draft_syarat"
                                                                            accept="application/pdf" onchange="submit()">
                                                                        <input type="hidden" name="dokumen_id"
                                                                            value="{{ $dst->dokumen_id }}">
                                                                    </form>
                                                                @elseif (isset($dst->dokumen_file) && $dst->verified == 1)
                                                                    {{-- <a class="btn btn-block btn-sm btn-outline-info"
                                                                        href="{{ route('daftar-tugas-akhir.show', $dst->dokumen_id) }}">
                                                                        <i class="fas fa-eye mr-md-2"></i>View</a> --}}
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

                @if ($memenuhiSyarat && $verifikasiPembimbing)
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col">
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h5 class="m-0">Daftar Sidang</h5>
                                    </div>
                                    @if (!isset($partner) || (isset($partner) && $partner_valid == 1))
                                        <form action="{{ route('daftar-tugas-akhir.daftar') }}" method="POST">
                                            @csrf
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>Judul Final</label>
                                                    <input type="text" name="judulFinal" class="form-control"
                                                        placeholder="Masukkan Judul Final"
                                                        value="{{ $mahasiswa->ta_judul }}">
                                                    <span class="text-danger">Ubah judul jika terdapat perubahan</span>
                                                </div>
                                                <div class="form-group">
                                                    <label>Pilih Jadwal Sidang</label>
                                                    <select name="jadwal" class="form-control js-example-basic-single">
                                                        @foreach ($jadwal as $jd)
                                                            @if (!in_array($jd->jadwal_id, $jadwalAda))
                                                                <option value={{ $jd->jadwal_id }}>Sesi
                                                                    {{ $jd->sesi_nama }}
                                                                    @if ($jd->hari_sidang == 'Jumat')
                                                                        ({{ $jd->sesi_waktu_mulai_jumat }}-{{ $jd->sesi_waktu_selesai_jumat }})
                                                                    @else
                                                                        ({{ $jd->sesi_waktu_mulai }}-{{ $jd->sesi_waktu_selesai }})
                                                                    @endif
                                                                    -
                                                                    {{ $jd->tgl_sidang }}
                                                                    -
                                                                    {{ $jd->ruangan_nama }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <a href="#" class="btn btn-info btn-block btn-flat confirm-upload">
                                                    <i class="fas fa-user-plus"></i>
                                                    Daftar Sidang
                                                </a>
                                            </div>
                                        </form>
                                    @else
                                        <div class="card-body">
                                            <div class="alert alert-danger">
                                                Anggota kelompok anda belum eligible untuk mendaftar sidang. Periksa semua
                                                syarat terlebih dahulu.
                                            </div>
                                        </div>
                                    @endif
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
                                        <h5 class="m-0">Daftar Sidang</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-warning alert-dismissible">
                                            <h5 class="text-bold"><i class="icon fas fa-exclamation-triangle"></i>
                                                Perhatian</h5>
                                            Anda belum dapat mendaftar sidang.
                                            @if (!$memenuhiSyarat)
                                                Semua syarat wajib diverifikasi terlebih dahulu.
                                            @elseif (!$verifikasiPembimbing)
                                                Anda belum disetujui untuk Daftar Sidang
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <div class="card card-primary card-outline">
                                <div class="card-body">
                                    <div class="alert alert-warning alert-dismissible">
                                        <h5 class="text-bold"><i class="icon fas fa-exclamation-triangle"></i> Jumlah
                                            Bimbingan Belum Terpenuhi</h5>
                                        Minimal {{ $masterJumlah }} kali bimbingan dari masing-masing pembimbing
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    @foreach ($dokumenSyaratTa as $dst)
        <!-- Modal -->
        <div class="modal fade" id="modal-{{ $dst->dokumen_id }}" tabindex="-1" role="dialog"
            aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">{{ $dst->dokumen_syarat }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{-- <p>{{ $dst->dokumen_file_original }}</p> --}}
                        <embed
                            src="/stream-document/{{ encrypt(env('APP_FILE_SYARAT_TA_PATH') . $dst->dokumen_file) . '?dl=0&filename=' . $dst->dokumen_file }}"
                            type="application/pdf" width="100%" height="600px">
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('.toast').toast('show')

        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });

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
