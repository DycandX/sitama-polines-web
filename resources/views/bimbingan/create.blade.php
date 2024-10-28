@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0">Tambah Bimbingan</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('bimbingan.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Tambah Bimbingan</li>
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
                        <h5 class="card-title m-0">Tambah Bimbingan</h5>
                        <div class="card-tools">
                            <a href="{{ route('bimbingan.index') }}" class="btn btn-tool"><i class="fas fa-arrow-left"></i></a>
                        </div>
                    </div>
                    <form id="formTambahBimbingan" action="{{ route('bimbingan.store') }}" method="post">
                        @csrf
                        <div class="card-body">
                        @for($i=1; $i<=2; $i++)
                        <div class="form-group">
                            <label>Mahasiswa {{ $i }} @if($i == 2) (opsional) @endif</label>
                            <select name="mhs{{ $i }}" class="form-control">
                                <option value=""></option>
                                @foreach($ta_mahasiswa as $item)
                                    @if(!in_array($item->ta_id, $taIdAda))
                                        <option value="{{ $item->ta_id }}">{{ $item->mhs_nim . ' - ' . $item->mhs_nama }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        @endfor
                            <div class="form-group">
                                <label>Dosen Pembimbing 1</label>
                                <select name="dosen_pembimbing_1" class="form-control">
                                    <option value=""></option>
                                    @foreach($dosen as $pembimbing)
                                    <option value="{{ $pembimbing->dosen_nip }}">{{ $pembimbing->dosen_nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Dosen Pembimbing 2</label>
                                <select name="dosen_pembimbing_2" class="form-control">
                                    <option value=""></option>
                                    @foreach($dosen as $pembimbing)
                                    <option value="{{ $pembimbing->dosen_nip }}">{{ $pembimbing->dosen_nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{--<div class="form-group">
                                <label>Tahun Akademik</label>
                                <select name="tahun_akademik" class="form-control">
                                <option value=""></option>
                                    <option value="2020/2021">2020/2021</option>
                                    <option value="2021/2022">2021/2022</option>
                                    <option value="2022/2023">2022/2023</option>
                                    <option value="2023/2024">2023/2024</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Judul Tugas Akhir</label>
                                <input type="text" name="ta_judul" class="form-control" placeholder="Masukkan Judul Tugas Akhir">
                                <select name="ta_judul" class="form-control">
                                    <option value=""></option>
                                    @foreach($tas as $ta)
                                    <option value="{{ $ta->ta_judul }}">{{ $ta->ta_judul }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <div class="input-group">
                                    <input type="checkbox" name="verified" data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                </div>
                            </div>--}}
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info btn-block btn-flat"><i class="fa fa-save"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Reset nilai input saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('formTambahBimbingan').reset();
    });
</script>
@endpush
