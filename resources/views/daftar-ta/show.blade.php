@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">
                        View Syarat
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
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col"><a href="{{ asset('storage/syarat_ta/' . $dokumenSyaratTa->dokumen_file) }}" target="_blank">{{ $dokumenSyaratTa->dokumen_file_original }}</a></div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <embed src="{{ asset('storage/syarat_ta/' . $dokumenSyaratTa->dokumen_file) }}" type="application/pdf" width="100%" height="600px">
                                </div>
                            </div>
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
