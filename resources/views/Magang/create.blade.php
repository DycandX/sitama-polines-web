@extends('layouts.app')
@section('css')
@endsection

@section('content')
<!-- Tampilkan pesan sukses -->

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Plotting Magang</h4>
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
                                <a href="{{ route('magang.index') }}" class="btn btn-tool"><i class="fas fa-arrow-alt-circle-left"></i></a>
                            </div>
                        </div>
                        <form action="{{ route('magang.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="mhs_nim">Pilih Mahasiswa</label>
                                    <select name="mhs_nim" class="form-control" id="mhs_nim">
                                        <option value="">Pilih Nama</option>
                                        @foreach($mahasiswas as $item)
                                        <option value="{{ $item->mhs_nim }}">{{ $item->nama_id }}-{{ $item->mhs_nim }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="dosen_nip">Nama Dosen</label>
                                    <select name="dosen_nip" class="form-control" id="dosen_nip">
                                        <option value="">Pilih Dosen</option>
                                        @foreach($dosens as $item)
                                        <option value="{{ $item->dosen_nip }}">{{ $item->dosen_nama }}-{{ $item->dosen_nip }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" href="{{ route('magang.index') }}" class="btn btn-success">Simpan</button>
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


<!-- /.content -->
</body>

</html>
@endsection