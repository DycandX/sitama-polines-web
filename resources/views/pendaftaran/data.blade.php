@extends('layouts.app')
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush
@php
    use Carbon\Carbon;
@endphp
@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <h1>Data Mahasiswa</h1>
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
                            <div class="card-tools">
                                <a href="{{ route('daftar-magang.create') }}" class="btn btn-sm btn-success"><i
                                        class="fas fa-plus-circle"></i> Tambah Magang</a>
                            </div>
                            <h5 class="m-0">Data Pribadi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <p>Nama </p>
                                    <p>Nim </p>
                                    @foreach ($datamhs as $item)
                                        @for ($i = 0; $i < count($item->nama_industri); $i++)
                                            <p>Industri{{ $i > 0 ? ' ' . ($i + 1) : '' }}</p>
                                            <p>Tanggal Mulai</p>
                                            <p>Tanggal Selesai</p>
                                        @endfor
                                    @endforeach
                                </div>
                                <div class="col-6">
                                @foreach ($datamhs as $item)
                                    <p>: {{ $item->mhs_nama }}</p>
                                    <p>: {{ $item->mhs_nim }}</p>
                                    @for ($i = 0; $i < count($item->nama_industri); $i++)
                                        <p>: {{ $item->nama_industri[$i] }}</p>
                                        <p>: {{ $item->tgl_mulai[$i] ? Carbon::parse($item->tgl_mulai[$i])->locale('id')->translatedFormat('d F Y') : '' }}</p>
                                        <p>: {{ $item->tgl_selesai[$i] ? Carbon::parse($item->tgl_selesai[$i])->locale('id')->translatedFormat('d F Y') : '' }}</p>
                                    @endfor
                                @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="m-0">Data Pembimbing</h5>
                        </div>
                        <div class="card-body">
                            @foreach ($datadosen as $index)
                            @if($index->dosen_nip == null)
                                <span>Belum ada Data Dosen Pembimbing</span>
                            @else
                                <div class="row">
                                    <div class="col-6">
                                        <p>Nama Dosen </p>
                                        <p>NIP Dosen </p>
                                    </div>
                                    <div class="col-6">
                                        <p> :            {{ $index->dosen_nama }}</p>
                                        <p> :            {{ $index->dosen_nip }}</p>
                                    </div>
                                </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection