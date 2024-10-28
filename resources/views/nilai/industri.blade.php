@extends('layouts.app')
@php
    use Carbon\Carbon;
@endphp
@section('content')
    <style>
        .label-span-group {
            display: flex;
            align-items: center;
        }
        .label-span-group label {
            min-width: 150px; /* Sesuaikan sesuai kebutuhan */
            margin-right: 20px; /* Sesuaikan sesuai kebutuhan */
        }
        .label-span-group select {
            width: 200px; /* Sesuaikan lebar sesuai kebutuhan */
        }
    </style>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Penilaian Industri</h4>
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
                            <h5 class="card-title m-0"></h5>
                            <div class="card-tools">
                                <a href="{{ route('nilai-dosen-magang.index') }}" class="btn btn-tool"><i
                                        class="fas fa-arrow-alt-circle-left"></i></a>
                            </div>
                        </div>
                        <form action="{{ route('nilai.update') }}" method="post">
                          @csrf
                          @method('PUT')
                          @foreach ($nilai as $item)
                            <div class="card-body">
                                <div class="border-top label-span-group">
                                    <label>Nama</label>
                                    <span>: {{ $item->mhs_nama }}</span>
                                </div>
                                <div class="border-top label-span-group">
                                    <label>NIM</label>
                                    <span>: {{ $item->mhs_nim }}</span>
                                </div>
                                <div class="border-top label-span-group">
                                    <label>Industri</label>
                                    <span>: {{ $item->nama_industri }}</span>
                                </div>
                                <div class="border-top label-span-group">
                                    <label>Tanggal Mulai</label>
                                    <span>: {{ $item->tgl_mulai ? Carbon::parse($item->tgl_mulai)->locale('id')->translatedFormat('d F Y') : '' }}</span>
                                </div>
                                <div class="border-top label-span-group">
                                    <label>Tanggal Selesai</label>
                                    <span>: {{ $item->tgl_selesai ? Carbon::parse($item->tgl_selesai)->locale('id')->translatedFormat('d F Y') : '' }}</span>
                                </div>
                               <div class="border-top label-span-group">
                                    <label>Nilai</label>
                                    <select name="nilai[{{ $item->magang_industri_id }}]" class="form-control">
                                        @for ($i = 100; $i >= 1; $i--)
                                            <option value="{{ $i }}" {{ isset($item->nilai) && $item->nilai == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="magang_id[{{ $item->magang_industri_id }}]" value="{{ $item->magang_id }}">
                          @endforeach
                          <div class="card-footer">
                                <button type="submit" class="btn btn-info btn-block btn-flat validasi-button"><i class="fa fa-save"></i>
                                    Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
