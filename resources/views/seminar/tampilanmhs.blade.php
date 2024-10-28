@extends('layouts.app')
@php
    use Carbon\Carbon;
@endphp
@section('content')
    @push('css')
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
       
    @endpush
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 class="m-0 text">Jadwal Seminar</h3>
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
                <div class="col-sm-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="m-0">Informasi Seminar Magang</h5>
                        </div>
                        
                        <div class="card-body">
                            @forelse ($seminar as $item)
                                <div class="row">
                                    <div class="col-md-4 col-sm-6">
                                        <h6 class="text-bold">Tanggal</h6>
                                    </div>
                                    <div class="col col-sm-6">
                                        <p>: {{ $item->tgl_seminar ? Carbon::parse($item->tgl_seminar)->locale('id')->translatedFormat('l, d F Y') : '' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-sm-6">
                                        <h6 class="text-bold">Jam</h6>
                                    </div>
                                    <div class="col col-sm-6">
                                        <p>: {{ $item->waktu }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 col-sm-6">
                                        <h6 class="text-bold m-0">Ruangan</h6>
                                    </div>
                                    <div class="col col-sm-6">
                                        <p class="m-0">: {{ $item->ruangan_nama }}</p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 col-sm-6">
                                        <h6 class="text-bold m-0">Status</h6>
                                    </div>
                                    <div class="col col-sm-6">
                                        @if ($item->status_seminar == 0)
                                        <p class="m-0">: Belum  Melaksanakan Seminar</p>
                                        @else
                                        <p class="m-0">: Sudah Melaksanakan Seminar</p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <span>Anda Belum Dijadwalkan Seminar</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
