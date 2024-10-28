@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-uppercase">Tambah Sidang TA</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('ta.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Tambah Sidang TA</li>
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
                        <h5 class="m-0">Tambah Sidang TA</h5>
                        <div class="card-tools">
                            <a href="{{ route('ta.index') }}" class="btn btn-tool"><i class="fas fa-arrow-left"></i></a>
                        </div>
                    </div>
                    <form action="{{ route('ta.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="mahasiswa">Mahasiswa</label>
                                <input type="text" id="mahasiswa" name="mahasiswa" class="form-control" placeholder="Nama Mahasiswa">
                            </div>
                            <div class="form-group">
                                <label for="waktu">Waktu</label>
                                <input type="datetime-local" id="waktu" name="waktu" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="tempat">Tempat</label>
                                <input type="text" id="tempat" name="tempat" class="form-control" placeholder="Tempat Sidang">
                            </div>
                            <div class="form-group">
                                <label for="dosen_pembimbing">Dosen Pembimbing</label>
                                <input type="number" id="dosen_pembimbing" name="dosen_pembimbing" class="form-control" placeholder="Dosen Pembimbing">
                            </div>
                            <div class="form-group">
                                <label for="dosen_penguji">Dosen Penguji</label>
                                <input type="number" id="dosen_penguji" name="dosen_penguji" class="form-control" placeholder="Dosen Penguji">
                            </div>
                            <div class="form-group">
                                <label for="nilai_pembimbing">Nilai Dosen Pembimbing</label>
                                <input type="number" id="nilai_pembimbing" name="nilai_pembimbing" class="form-control" placeholder="Nilai Dosen Pembimbing">
                            </div>
                            <div class="form-group">
                                <label for="nilai_penguji">Nilai Dosen Penguji</label>
                                <input type="number" id="nilai_penguji" name="nilai_penguji" class="form-control" placeholder="Nilai Dosen Penguji">
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="Selesai">Selesai</option>
                                    <option value="Belum Selesai">Belum Selesai</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
