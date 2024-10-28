@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Sidang TA</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('ta.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Edit Sidang TA</li>
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
                        <h5 class="card-title m-0">Edit Sidang Ta</h5>
                        <div class="card-tools">
                            <a href="{{ route('bimbingan.index') }}" class="btn btn-tool"><i class="fas fa-arrow-left"></i></a>
                        </div>
                    </div>
                    {{-- {{dd($ta_mahasiswa)}} --}}
                    <form action="{{ route('bimbingan.update', $ta_mahasiswa->ta_id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label>Mahasiswa</label>
                                <select name="mhs_nim" class="form-control">
                                    <option disabled selected>Mahasiswa</option>
                                    @foreach($mhs as $item)
                                    <option value="{{ $item->mhs_nim }}" {{ $ta_mahasiswa->mhs_nim == $item->mhs_nim ? 'selected' : ''}}>{{ $item->mhs_nim . ' - ' . $item->mhs_nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Judul TA</label>
                                <input type="text" name="ta_judul" class="form-control" value="{{ $ta_mahasiswa->ta_judul }}">
                            </div>
                            <!-- Assuming you have a 'Dosen' model for pembimbing 1 and 2 -->
                            <div class="form-group">
                                <label>Dosen Pembimbing 1</label>
                                <select name="pembimbing_1" class="form-control">
                                    @foreach($dosen as $pembimbing)
                                    @if ($bimbingan[0]['dosen_nip']==$pembimbing->dosen_nip)
                                    <option selected='selected' value="{{ $pembimbing->dosen_nip }}">
                                        {{ $pembimbing->dosen_nama }}
                                    </option>
                                    @else
                                    <option value="{{ $pembimbing->dosen_nip }}">
                                        {{ $pembimbing->dosen_nama }}
                                    </option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Dosen Pembimbing 2</label>
                                <select name="pembimbing_2" class="form-control">
                                    @foreach($dosen as $pembimbing)
                                    @if ($bimbingan[1]['dosen_nip']==$pembimbing->dosen_nip)
                                    <option selected='selected' value="{{ $pembimbing->dosen_nip }}">
                                        {{ $pembimbing->dosen_nama }}
                                    </option>
                                    @else
                                    <option value="{{ $pembimbing->dosen_nip }}">
                                        {{ $pembimbing->dosen_nama }}
                                    </option>
                                    @endif

                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tahun Akademik</label>
                                <select name="tahun_akademik" class="form-control">
                                <option disabled selected>Tahun Akademik</option>
                                    <option value="2020-2021" {{ $ta_mahasiswa->tahun_akademik == '2020-2021' ? 'selected' : '' }}>2020-2021</option>
                                    <option value="2021-2022" {{ $ta_mahasiswa->tahun_akademik == '2021-2022' ? 'selected' : '' }}>2021-2022</option>
                                    <option value="2022-2023" {{ $ta_mahasiswa->tahun_akademik == '2022-2023' ? 'selected' : '' }}>2022-2023</option>
                                    <option value="2023-2024" {{ $ta_mahasiswa->tahun_akademik == '2023-2024' ? 'selected' : '' }}>2023-2024</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info btn-block btn-flat"><i class="fa fa-save"></i> Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection