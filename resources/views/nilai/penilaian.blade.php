@extends('layouts.app')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Penilaian Magang</h4>
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
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="m-0"></h5>
                    </div>
                    <div class="card-body">
                        <table id="datatable-main" class="table table-bordered table-striped">
                            <thead>
                            <th class="text-center">No</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th class="text-center">Nilai Dosen</th>
                            <th class="text-center">Nilai Industri</th>
                            <th class="text-center">Nilai Akhir</th>
                            <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach ($total as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{$item->mhs_nim}}</td>
                                    <td>{{ $item->mhs_nama }}</td>
                                    <td class="text-center">{{ $item->nilai_dosen !== null ? \App\Helpers\NilaiHelper::formatNilai($item->nilai_dosen) : '-' }}</td>
                                    <td class="text-center">{{ $item->nilai_industri !== null ? $item->nilai_industri : '-' }}</td>
                                    <td class="text-center">{{ $item->nilai_akhir !== null ? \App\Helpers\NilaiHelper::formatNilai($item->nilai_akhir) . ' (' . \App\Helpers\NilaiHelper::nilaiDeskripsi($item->nilai_akhir) . ')' : '-' }}</td>
                                    <td>
                                    <button type="button" class="btn btn-block btn-sm btn-outline-info"
                                        data-toggle="dropdown"><i class="fas fa-eye"></i>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                      <a class="dropdown-item" href="{{ route('nilai-dosen-magang.nilaidosen',  $item->magang_id) }}">Nilai Dosen</a>
                                      <a class="dropdown-item" href="{{ route('nilai-dosen-magang.nilaiindustri',  $item->magang_id) }}">Nilai Industri</a>
                                    </div>
                                    </td>
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
    <!-- /.container-fluid -->
</div>
@endsection


