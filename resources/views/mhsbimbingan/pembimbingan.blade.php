@extends('layouts.app')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Pembimbingan</h4>
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
                <div class="col">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title m-0">{{ $mahasiswa->mhs_nim . ' - ' . $mahasiswa->mhs_nama }}</h5>
                            <div class="card-tools">
                                <a href="{{ route('mhsbimbingan.index') }}" class="btn btn-sm btn-warning"><i
                                        class="fas fa-angle-double-left"></i> Kembali</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col table-responsive">
                                    <table id="datatable-bimbdsn" class="table-striped table-bordered table-hover table">
                                        <thead>
                                            <th>No</th>
                                            <th>Judul Bimbingan</th>
                                            <th>Deskripsi</th>
                                            <th>Tanggal</th>
                                            <th>File</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </thead>
                                        <tbody>
                                            @foreach ($bimbLog as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->bimb_judul }}</td>
                                                    <td>{{ $item->bimb_desc }}</td>
                                                    <td>{{ $item->format_tanggal }}</td>
                                                    <td class="text-center">
                                                        @if (isset($item->bimb_file))
                                                            <a href="/stream-document/{{ encrypt(env('APP_FILE_DRAFT_TA_PATH') . $item->bimb_file) . '?dl=0&filename=' . $item->bimb_file_original }}"
                                                                target="_blank"
                                                                class="btn btn-sm btn-primary my-tooltip top">
                                                                <i class="fa fa-eye"></i>
                                                                <span class="tooltiptext">
                                                                    Tampilkan File Bimbingan
                                                                </span>
                                                            </a>
                                                        @else
                                                            <span class="badge badge-primary">Tidak ada lampiran</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($item->bimb_status == 0)
                                                            <span class="badge badge-danger">Belum Valid</span>
                                                        @elseif ($item->bimb_status == 1)
                                                            <span class="badge badge-success">Valid</span>
                                                        @else
                                                            Invalid status
                                                        @endif
                                                    </td>
                                                    <td style="text-align: center;">
                                                        @if ($item->bimb_status == 0)
                                                            <form
                                                                action="{{ route('setujui-pembimbingan', $item->bimbingan_log_id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <button
                                                                    class="confirm-verif btn btn-block btn-sm btn-success my-tooltip top">
                                                                    <i class="fa fa-check"></i>
                                                                    <span class="tooltiptext">Validasi Bimbingan</span>
                                                                </button>
                                                            </form>
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
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="m-0">Jumlah Bimbingan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-bold">Pembimbing {{ $mahasiswa->urutan }}</h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-md-4">
                                    <p class="font-weight-bold m-0">{{ $mahasiswa->dosen_nama }}</p>
                                </div>
                                <div class="col">
                                    <p class="m-0">
                                        :
                                        {{ $bimbLogJumlah->where('bimb_status', 1)->count() }}/{{ $bimbLogJumlah->count() }}
                                        @if ($bimbLogJumlah->where('bimb_status', 1)->count() >= $masterJumlah)
                                            <span class="badge badge-success ml-1">Terpenuhi</span>
                                        @else
                                            <span class="badge badge-danger ml-1">Belum Terpenuhi</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $('.toast').toast('show')

        $(function() {
            $("#datatable-bimbdsn").DataTable({
                "responsive": true,
                "searching": true,
                lengthMenu: [
                    [10, 20, -1],
                    [10, 20, 'All']
                ],
                pageLength: 10,
            });
        });

        $('.confirm-verif').click(function(event) {
            var form = $(this).closest("form");
            event.preventDefault();
            swal({
                    title: 'Setujui kegiatan pembimbingan?',
                    icon: "success",
                    buttons: {
                        confirm: {
                            text: 'Ya',
                            className: 'btn-success'
                        },
                        cancel: 'Tidak',
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
