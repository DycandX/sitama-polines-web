@extends('layouts.app')
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush
@section('content')
<style>
        .label-span-group label {
            min-width: 150px; /* Sesuaikan sesuai kebutuhan */
            margin-right: 20px; /* Sesuaikan sesuai kebutuhan */
        }
        .label-span-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 0px;
        }

        .label-span-item {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 5px; /* Memberikan jarak antar baris */
        }

        .label-span-item label {
            min-width: 100px; /* Menentukan lebar minimum label untuk alignment */
        }
        .komponen-cell {
            max-width: 300px; 
            word-wrap: break-word;
            white-space: normal;
            text-align: left;
            
        }
</style>

    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                <h4 class="m-0">Penilaian Dosen</h4>
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
                            <div class="card-tools">
                              <a href="{{ route('nilai-dosen-magang.index') }}" class="btn btn-tool"><i class="fas fa-arrow-alt-circle-left"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                        <form action="{{ route('nilai.store') }}" method="post">
                        @csrf
                        @foreach($validasi as $item)
                            <div class="label-span-group">
                                <div class="label-span-item">
                                    <label>Nama</label>
                                    <span>: {{ $item->mhs_nama }}</span>
                                </div>
                                <div class="label-span-item">
                                    <label>NIM</label>
                                    <span>: {{ $item->mhs_nim }}</span>
                                </div>
                            </div>
                            <div class="label-span-group">
                                @for ($i = 0; $i < count($item->nama_industri); $i++)
                                    <div class="label-span-item">
                                        <label>Industri{{ $i > 0 ? ' ' . ($i + 1) : '' }}</label>
                                        <span>: {{ $item->nama_industri[$i] }}</span>
                                    </div>
                                @endfor
                            </div>
                        @endforeach
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Point</th>
                                            <th scope="col">Komponen</th>
                                            <th scope="col">Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($komponen as $item)
                                        <tr>
                                            <td class="point-label">{{ $item->nama_komponen }}</td>
                                            <td class="komponen-cell">{!! nl2br(e($item->keterangan)) !!}</td>
                                            <td>
                                                <div class="form-group">
                                                    <select class="form-control" id="sel1" name="nilai[{{$item->komponen_detail_id}}]" style="100px;">
                                                        @for ($i = 10; $i >= 1; $i--)
                                                            <option value="{{ $i }}">{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                        <input type="hidden" name="id" value="{{ session('magang_id') }}">
                                    </tbody>
                                </table>
                                <div class="keterangan">
                                    <p class="font-weight-bold">Keterangan</p>
                                    <p class="font-weight-normal">* Untuk Point Proposal Total punya 20% Bobot</p>
                                    <p class="font-weight-normal">* Untuk Point Laporan Total punya 80% Bobot</p>
                                    <p class="font-weight-normal">* Nilai dosen dihitung dari nilai Proposal + Laporan</p>
                                </div>
                                <div class="card-footer">
                                        <button type="submit" class="btn btn-info btn-block btn-flat validasi-button"><i class="fa fa-save"></i>
                                            Simpan</button>
                                </div>
                                {{-- <div class="pagination">
                                    <span>Showing 1 to 3 of 3 entries</span>
                                    <a href="#" class="prev">Previous</a>
                                    <a href="#" class="active">1</a>
                                    <a href="#">Next</a>
                                </div> --}}
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection