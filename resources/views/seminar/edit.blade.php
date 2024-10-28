@extends('layouts.app')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Tambah Jadwal Seminar</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                </ol>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="m-0"></h5>
                            <div class="card-tools">
                                <a href="{{ route('seminar.index') }}" class="btn btn-tool"><i class="fas fa-arrow-alt-circle-left"></i></a>
                            </div>
                        </div>
                        <form action="{{ route('seminar.ubah') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="mhs_nim">Nama Mahasiswa</label>
                                    @foreach($magangs as $item)
                                    <input class="form-control" type="text" placeholder="{{ $item->mhs_nim }} - {{ $item->mhs_nama }}" readonly>
                                    <input type="hidden" name="magang_id" value="{{ $item->magang_id }}">
                                    @endforeach
                                </div>
                                <div class="form-group">
                                    <label for="tgl_seminar">Tanggal Seminar</label>
                                    <input type="date" name="tgl_seminar" class="form-control" id="tgl_seminar" placeholder="Masukkan Tanggal">
                                </div>
                                <div class="form-group">
                                    <label for="ruangan_nama">Tempat Seminar</label>
                                    <select name="ruangan_id" class="form-control" id="ruangan_nama">
                                        @foreach($ruangan_ta as $item)
                                        <option value="{{ $item->ruangan_id }}">{{ $item->ruangan_nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="waktu">Waktu</label>
                                    <input type="time" name="waktu" class="form-control" id="waktu" placeholder="Masukkan Waktu">
                                </div>
                                <button type="submit" href="{{ route('seminar.index') }}" class="btn btn-success">Simpan</button>
                        </form>
                        @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                        @endif
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
</div>


@endsection