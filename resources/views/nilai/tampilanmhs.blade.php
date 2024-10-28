
@extends('layouts.app')
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush
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
        .komponen-cell {
            max-width: 300px; 
            word-wrap: break-word;
            white-space: normal;
            text-align: left;
            
        }
        
  .pagination {
    display: flex;
    justify-content: flex-end;
    padding: 10px;
  }
  .pagination span {
    margin-right: auto;
    color: black;
  }
  .pagination a {
    padding: 5px 10px;
    border: 1px solid #ccc;
    text-decoration: none;
    color: #333;
  }
  .card-tools {
    display: block;
    text-align: end;
    margin-bottom: 20px;
  }
  .dropdown-toggle.btn-transparent {
    background-color: transparent !important;
    border-color: transparent !important;
    color: #007bff;
  }
  .dropdown-toggle.btn-transparent:hover {
    background-color: rgba(0, 123, 255, 0.1) !important;
    color: #0056b3;
  }
  .label-span-group {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
  }
  .label-span-group label {
    margin-left: 100px;
  }
  .label-span-group span {
    margin-right: 40px;
    font-weight: bold;
  }
</style>

    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                <h4 class="m-0">Nilai Mahasiswa</h4>
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
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                          <th class="text-center">No</th>
                                          <th class="text-center">Point</th>
                                          <th class="text-center">Komponen</th>
                                          <th class="text-center">Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      @foreach($komponen as $item)
                                        <tr>
                                          <td class="text-center">{{ $loop->iteration }}</td>
                                          <td>{{$item->nama_komponen}}</td>
                                          <td>{!! nl2br(e($item->keterangan)) !!}</td>
                                          <td class="text-center">{{$item->nilai}}</td>
                                        </tr>
                                      @endforeach
                                    </tbody>
                                </table>
                                <div class="keterangan">
                                    <p class="font-weight-bold">Bobot Nilai</p>
                                    <p class="font-weight-normal">* Untuk Point Proposal Total punya 20% Bobot</p>
                                    <p class="font-weight-normal">* Untuk Point Laporan Total punya 80% Bobot</p>
                                    <p class="font-weight-normal">* Nilai dosen dihitung dari nilai Proposal + Laporan</p>
                                </div>
                                @foreach ($data as $item)
                                <div class="card-body">
                                    <div class="border-bottom label-span-group">
                                        <label>Total Nilai Dosen</label>
                                        <span>{{ $item->nilai_dosen !== null ? \App\Helpers\NilaiHelper::formatNilai($item->nilai_dosen) : '-' }}</span>
                                    </div>
                                    <div class="border-bottom label-span-group">
                                        <label>Total Nilai Industri</label>
                                        <span>{{$item->nilai_industri}}</span>
                                    </div>
                                    <div class="border-bottom label-span-group">
                                        <label>Nilai Akhir</label>
                                        <span>{{ $item->nilai_akhir !== null ? \App\Helpers\NilaiHelper::formatNilai($item->nilai_akhir) . ' (' . \App\Helpers\NilaiHelper::nilaiDeskripsi($item->nilai_akhir) . ')' : '-' }}</span>
                                    </div>
                                @endforeach
                                </div>
                                {{-- <div class="pagination">
                                    <span>Showing 1 to 3 of 3 entries</span>
                                    <a href="#" class="prev">Previous</a>
                                    <a href="#" class="active">1</a>
                                    <a href="#">Next</a>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection