@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">
                        @if (!isset($dokumenSyaratTa->dokumen_file))
                            Upload Syarat
                        @else
                            Edit Syarat
                        @endif
                    </h4>
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
                            <h5 class="card-title m-0">{{ $dokumenSyaratTa->dokumen_syarat }}</h5>
                            <div class="card-tools">
                                <a href="{{ route('daftar-tugas-akhir.index') }}" class="btn btn-tool"><i class="fas fa-arrow-alt-circle-left"></i></a>
                            </div>
                        </div>
                        <form action="{{ route('daftar-tugas-akhir.store') }}" method="POST" enctype="multipart/form-data">
                            <div class="card-body">
                                @csrf
                                <input type="hidden" name="dokumenId" value="{{ $dokumenSyaratTa->dokumen_id }}">
                                <div class="form-group">
                                    <label for="draft_syarat">File Lampiran <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input class="form-control" type="file" id="draft_syarat" name="draft_syarat" accept="application/pdf">
                                        <span class="text-danger">Format file : PDF(Max 2MB)</span>
                                        @error('draft')
                                            <div class="invalid-feedback" role="alert">
                                                <span>{{ $message }}</span>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                @if (isset($dokumenSyaratTa->dokumen_file))
                                    <div class="d-flex flex-column">
                                        <label>File Sebelumnya</label>
                                        <a class="mb-2" href="{{ asset('storage/syarat_ta/' . $dokumenSyaratTa->dokumen_file) }}" target="_blank">{{ $dokumenSyaratTa->dokumen_file_original }}</a>
                                    </div>
                                @endif
                                <div class="div">
                                    <label>Keterangan :</label>
                                    <p class="m-0 text-danger">* Wajib</p>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-info btn-block btn-flat"><i class="fa fa-save"></i>
                                    Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('js')
@endpush
