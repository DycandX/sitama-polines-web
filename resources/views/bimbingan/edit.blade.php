@extends('layouts.app')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Edit Bimbingan</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('bimbingan.index') }}">Home</a></li>
                        <li class="breadcrumb-item active">Edit Bimbingan</li>
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
                            <h5 class="card-title m-0">Edit Bimbingan</h5>
                            <div class="card-tools">
                                <a href="{{ route('bimbingan.index') }}" class="btn btn-tool"><i
                                        class="fas fa-arrow-left"></i></a>
                            </div>
                        </div>
                        <form action="{{ route('bimbingan.update', $ta_mahasiswa->ta_id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Mahasiswa</label>
                                    <select name="mhs_nim" class="form-control">
                                        <option disabled selected>Mahasiswa</option>
                                        @foreach ($mhs as $item)
                                            <option value="{{ $item->mhs_nim }}"
                                                {{ $ta_mahasiswa->mhs_nim == $item->mhs_nim ? 'selected' : '' }}>
                                                {{ $item->mhs_nim . ' - ' . $item->mhs_nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Judul TA</label>
                                    <input type="text" name="ta_judul" class="form-control"
                                        value="{{ $ta_mahasiswa->ta_judul }}">
                                </div>
                                @if ($bimbingan->isEmpty())
                                    @for ($i = 1; $i <= 2; $i++)
                                        <div class="form-group">
                                            <label>Dosen Pembimbing {{ $i }}</label>
                                            <select name="pembimbing_{{ $i }}" class="form-control select2">
                                                <option value="">Pilih Pembimbing {{ $i }}</option>
                                                @foreach ($dosen as $pembimbing)
                                                    <option value="{{ $pembimbing->dosen_nip }}">
                                                        {{ $pembimbing->dosen_nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endfor
                                @else
                                    @foreach ($bimbingan as $bimb)
                                        <div class="form-group">
                                            <label>Dosen Pembimbing {{ $loop->iteration }}</label>
                                            <select name="pembimbing_{{ $loop->iteration }}" class="form-control select2">
                                                @foreach ($dosen as $pembimbing)
                                                    <option value="{{ $pembimbing->dosen_nip }}"
                                                        {{ $bimb->dosen_nip == $pembimbing->dosen_nip ? 'selected' : '' }}>
                                                        {{ $pembimbing->dosen_nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endforeach
                                @endif

                                {{-- <div class="form-group">
                                <label>Dosen Pembimbing 1</label>
                                <select name="pembimbing_1" class="form-control">
                                    @foreach ($dosen as $pembimbing)
                                    @if ($bimbingan[0]['dosen_nip'] == $pembimbing->dosen_nip)
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
                                    @foreach ($dosen as $pembimbing)
                                    @if ($bimbingan[1]['dosen_nip'] == $pembimbing->dosen_nip)
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
                            </div> --}}
                                <div class="form-group">
                                    <label>Tahun Akademik</label>
                                    <select name="tahun_akademik" class="form-control">
                                        <option disabled selected>Tahun Akademik</option>
                                        <option value="2020/2021"
                                            {{ $ta_mahasiswa->tahun_akademik == '2020/2021' ? 'selected' : '' }}>2020/2021
                                        </option>
                                        <option value="2021/2022"
                                            {{ $ta_mahasiswa->tahun_akademik == '2021/2022' ? 'selected' : '' }}>2021/2022
                                        </option>
                                        <option value="2022/2023"
                                            {{ $ta_mahasiswa->tahun_akademik == '2022/2023' ? 'selected' : '' }}>2022/2023
                                        </option>
                                        <option value="2023/2024"
                                            {{ $ta_mahasiswa->tahun_akademik == '2023/2024' ? 'selected' : '' }}>2023/2024
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-info btn-block btn-flat"><i class="fa fa-save"></i>
                                    Perbarui</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".select2").select2();
            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });
        });
    </script>
@endpush
