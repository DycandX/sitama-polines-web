@extends('layouts.app')
@section('css')
@endsection

@section('content')
<!-- Tampilkan pesan sukses -->

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Detail Persyaratan</h4>
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
                        <div class="card-body">
                            <div class="card-body">

                                <table id="datatable" class="table table-bordered table-striped">
                                    <thead>
                                        <th>Surat Keterangan Magang</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($syarat as $item)
                                        @if ($item->tipe == 3)
                                        <tr>
                                            <td>
                                                <a href="{{ asset('syarat/' . $item->file_magang) }}" target="_blank">
                                                    <i class="fas fa-file"></i> {{ $item->file_magang }}
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
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