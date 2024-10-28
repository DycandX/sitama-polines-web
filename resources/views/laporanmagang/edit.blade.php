@extends('layouts.app')
@section('content')
    @push('css')
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <style>
            /* Tambahkan border pada card */
            .card-header {
                border-top: 5px solid #020238;
                background-color: #fff;
                /* Tambahkan warna latar belakang */
            }
        </style>
    @endpush
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 class="m-0 text-bold">Edit Laporan Magang</h3>
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
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title m-0"></h5>
                            <div class="card-tools">
                                <a href="{{ route('laporanmagang.index') }}" class="btn btn-tool"><i
                                        class="fas fa-arrow-alt-circle-left"></i></a>
                            </div>
                        </div>
                        <form action="{{ route('laporanmagang.update', $laporanmagang->laporan_id) }}" method="post"
                            enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            @method('PATCH')
                            <div class="card-body">
                                <input type="hidden" name="id" id="id" value="{{ $laporanmagang->id }}">

                                <div class="mb-3">
                                    <label for="jenis_dokumen" class="form-label">Jenis Dokumen</label>
                                    <select class="form-control" name="jenis_dokumen" aria-label="Default select example">
                                        <option value="1" {{ $laporanmagang->tipe == 1 ? 'selected' : '' }}>Proposal
                                        </option>
                                        <option value="2" {{ $laporanmagang->tipe == 2 ? 'selected' : '' }}>Laporan
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="magang_judul" class="form-label">Judul Dokumen Magang</label>
                                    <input type="text" name="magang_judul" id="magang_judul"
                                        value="{{ $laporanmagang->magang_judul }}" class="form-control"
                                        placeholder="Masukkan judul dokumen">
                                </div>

                                <div class="mb-3">
                                    <label for="file_magang" class="form-label">File</label>
                                    <input type="file" name="file_magang" id="file_magang"
                                        value="{{ $laporanmagang->file_magang }}" class="form-control">

                                </div>

                                {{-- <a href="{{ route('laporanmagang.index') }}" class="btn btn-secondary">Close</a>
                            <input type="submit" value="Update" class="btn btn-success"><br> --}}
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success btn-block"><i class="fa fa-save"></i>
                                    Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endpush
