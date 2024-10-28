@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">View Bimbingan</h4>
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
                <div class="col">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title m-0"></h5>
                            <div class="card-tools">
                                <a href="{{ route('bimbingan-mahasiswa.index') }}" class="btn btn-tool"><i class="fas fa-arrow-alt-circle-left"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Judul Bimbingan</label>
                                <input type="text" class="form-control" value="{{ old('judul', $bimbLog->bimb_judul) }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea class="form-control" rows="6" name="desk" style="resize: none;" readonly>{{ old('desk', $bimbLog->bimb_desc) }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Pembimbing</label>
                                @foreach ($mahasiswa->dosen as $pembimbing)
                                @if ($pembimbing['bimbingan_id'] == $bimbLog->bimbingan_id)
                                <input class="form-control" type="text" value="Pembimbing {{ $loop->iteration . ' - ' . $pembimbing['dosen_nama'] }}" readonly>
                                @endif
                                @endforeach
                            </div>
                            <div class="form-group">
                                <label>Jadwal Bimbingan</label>
                                <input type="text" class="form-control" value="{{ $bimbLog->bimb_tgl }}" readonly>
                            </div>
                            <div class="d-flex flex-column">
                                <label>File Sebelumnya</label>
                                @if (isset($bimbLog->bimb_file))
                                    <a href="{{ asset('storage/draft_ta/' . $bimbLog->bimb_file) }}" target="_blank">{{ $bimbLog->bimb_file_original }}</a>
                                @else
                                    <p class="m-0">Tidak Ada Lampiran</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush
