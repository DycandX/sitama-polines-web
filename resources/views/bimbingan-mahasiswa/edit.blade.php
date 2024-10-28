@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Edit Bimbingan</h4>
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
                        <form action="{{ route('bimbingan-mahasiswa.update', $bimbLog->bimbingan_log_id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Judul Bimbingan <span class="text-danger">*</span></label>
                                    <input type="text" name="judul" class="form-control @error('judul')is-invalid @enderror" placeholder="Masukkan Judul Bimbingan" value="{{ old('judul', $bimbLog->bimb_judul) }}">
                                    @error('judul')
                                        <div class="invalid-feedback" role="alert">
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('desk')is-invalid @enderror" placeholder="Masukkan Deskripsi" rows="6" name="desk" style="resize: none;">{{ old('desk', $bimbLog->bimb_desc) }}</textarea>
                                    @error('desk')
                                        <div class="invalid-feedback" role="alert">
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Pembimbing <span class="text-danger">*</span></label>
                                    <select class="custom-select @error('pembimbing')is-invalid @enderror" name="pembimbing">
                                        <option disabled selected>Pilih Pembimbing</option>
                                        @foreach ($mahasiswa->dosen as $pembimbing)
                                            <option value="{{ $pembimbing['bimbingan_id'] }}" {{ $bimbLog->bimbingan_id == $pembimbing['bimbingan_id'] ? 'selected' : '' }}>Pembimbing {{ $loop->iteration . ' - ' . $pembimbing['dosen_nama'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('pembimbing')
                                        <div class="invalid-feedback" role="alert">
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Jadwal Bimbingan <span class="text-danger">*</span></label>
                                    <input type="date" name="tgl" class="form-control @error('tgl')is-invalid @enderror" placeholder="Masukkan Jadwal Bimbingan" value="{{ $bimbLog->bimb_tgl }}">
                                    @error('tgl')
                                        <div class="invalid-feedback" role="alert">
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>File Lampiran</label>
                                    <div class="custom-file">
                                        <input class="form-control" type="file" id="draft" name="draft" accept="application/pdf">
                                        <span class="text-danger">Format file : PDF(Max 2MB)</span>
                                        @error('draft')
                                            <div class="invalid-feedback" role="alert">
                                                <span>{{ $message }}</span>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <label>File Sebelumnya</label>
                                    @if (isset($bimbLog->bimb_file))
                                        <a href="{{ asset('storage/draft_ta/' . $bimbLog->bimb_file) }}" target="_blank">{{ $bimbLog->bimb_file_original }}</a>
                                    @else
                                        <p class="m-0">Tidak Ada Lampiran</p>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="text-bold">Keterangan :</h6>
                                    <p class="text-danger m-0">* Wajib</p>
                                </div>
                            </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-info btn-block btn-flat"><i class="fa fa-save"></i>
                                    Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush
