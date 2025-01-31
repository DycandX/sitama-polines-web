@extends('layouts.app')
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush
@section('content')
@php
    use Carbon\Carbon;
@endphp
<style>
        .label-span-group label {
            min-width: 150px; /* Sesuaikan sesuai kebutuhan */
            margin-right: 20px; /* Sesuaikan sesuai kebutuhan */
        }
        .label-span-group {
            display: flex;
            flex-direction: column;
        }

        .label-span-item {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 5px; /* Memberikan jarak antar baris */
        }

        .label-span-item label {
            min-width: 100px; /* Menentukan lebar minimum label untuk alignment */
        }

</style>

    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                <h4 class="m-0">LogBook Mahasiswa</h4>
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
                              <a href="{{ route('bimbingan-dosen-magang.index') }}" class="btn btn-tool"><i class="fas fa-arrow-alt-circle-left"></i></a>
                            </div>
                    </div>
                        <div class="card-body">
                        @foreach($validasi as $item)
                            <div class="label-span-group">
                                <div class="label-span-item">
                                    <label>Nama</label>
                                    <span>: {{ $item->mhs_nama }}</span>
                                </div>
                                <div class="label-span-item">
                                    <label>NIM</label>
                                    <span>: {{ $item->mhs_nim }}</span>
                                </div>
                            </div>
                            <div class="label-span-group">
                                @for ($i = 0; $i < count($item->nama_industri); $i++)
                                    <div class="label-span-item">
                                        <label>Industri{{ $i > 0 ? ' ' . ($i + 1) : '' }}</label>
                                        <span>: {{ $item->nama_industri[$i] }}</span>
                                    </div>
                                @endfor
                            </div>
                        @endforeach
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                          <th class="text-center">No</th>
                                          <th>Hari/Tanggal</th>
                                          <th>Kegiatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($logbook as $item)
                                      <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $item->tgl_kegiatan ? Carbon::parse($item->tgl_kegiatan)->locale('id')->translatedFormat('l, d F Y') : '' }}</td>
                                        <td>{{ $item->kegiatan }}</td>
                                      </tr>
                                    @endforeach
                                    </tbody>
                                </table>
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
    </div>
@endsection