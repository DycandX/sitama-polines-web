@extends('layouts.app')
@section('css')
@endsection

@section('content')
<!-- Tampilkan pesan sukses -->

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Detail Penilaian</h4>
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
                                <div class="float-left">

                                </div>
                                <a href="{{ route('magang.index') }}" class="btn btn-tool"><i class="fas fa-arrow-alt-circle-left"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered table-striped">
                                <thead>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Nilai Dosen Pembimbing</th>
                                    <th>Nilai Industri</th>
                                    <th>Nilai Akhir</th>
                                </thead>
                                <tbody>
                                    @foreach ($nilai as $item)
                                    <tr>
                                        <td>{{ $item->mhs_nim }}</td>
                                        <td>{{ $item->mhs_nama }}</td>
                                        <td>{{ $item->nilai_dosen }}</td>
                                        <td>{{ $item->nilai_industri }}</td>
                                        <td>{{ $item->nilai_akhir }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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