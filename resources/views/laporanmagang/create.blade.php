@extends('layouts.app')
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <h3>Tambahkan File Magang</h3>
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
                            <div class="card-tools">
                                <a href="{{ route('laporanmagang.index') }}" class="btn btn-tool"><i
                                        class="fas fa-arrow-alt-circle-left"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                        <form action="{{ url('laporanmagang') }}" method="post" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="jenis_dokumen" class="form-label">Jenis Dokumen</label>
                                    <select class="form-control" name="jenis_dokumen" aria-label="Default select example">
                                        <option selected>Pilih Jenis Dokumen</option>
                                        <option value="1">Proposal</option>
                                        <option value="2">Laporan</option>
                                        <option value="3">Dokumen Lain</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="magang_judul" class="form-label">Judul Dokumen Magang</label>
                                    <input type="text" name="magang_judul" id="magang_judul" class="form-control"
                                        placeholder="Masukkan judul dokumen">
                                </div>
                                <div class="mb-3">
                                    <label for="file_magang" class="form-label">File</label>
                                    <input type="file" name="file_magang" id="file_magang" class="form-control">
                                </div>
                                {{-- <a href="{{ route('laporanmagang.index') }}" class="btn btn-secondary">Close</a>
                                <input type="submit" value="Save" class="btn btn-success"><br> --}}
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-info btn-block btn-flat"><i class="fa fa-save"></i>
                                    Save</button>
                            </div>
                        </form>
                                {{-- <div class="pagination">
                                    <span>Showing 1 to 3 of 3 entries</span>
                                    <a href="#" class="prev">Previous</a>
                                    <a href="#" class="active">1</a>
                                    <a href="#">Next</a>
                                </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
