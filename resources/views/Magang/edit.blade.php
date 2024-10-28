@extends('layouts.app')
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
@endpush
@section('content')

<style>
     .judul {
            text-align: left;
            padding-left: 20px;
            /* Untuk memberikan sedikit padding di sebelah kiri judul */
            margin-bottom: 20px;
            /* Untuk memberikan jarak antara judul dan form */
        }

        .container {
            width: 100%;
            margin-left: 0 auto;
            /* Mengatur margin menjadi 0 auto untuk mengatur posisi ke tengah */
            padding: 20px;
            border: 1px solid #ccc;
            border-top: 5px solid #020238;
            background-color: #f8f9fa;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-judul {
            margin-bottom: 20px;
            /* Untuk memberikan jarak antara judul form dan input */
        }

        .label {
            display: block;
            margin-bottom: 5px;
        }

        .input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;

        }

        .button-container .btn {
            margin-right: 10px;
            background-color: rgba(2, 2, 56, 1);
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #218838;
        }
</style>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <h4>Plotting Magang</h4>
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
                                <a href="{{ route('magang.index') }}" class="btn btn-tool"><i class="fas fa-arrow-alt-circle-left"></i></a>
                            </div>
                    </div>
                        <div class="card-body">
                        <div class="daftar">
                            <form action="{{ route('magang.editDosen') }}" method="POST">
                                @csrf
                                <label for="nama_id">Nama Mahasiswa</label>
                                @foreach($magang as $item)
                                <input class="form-control" type="text" value="{{ $item->mhs_nama }}" readonly>
                                @endforeach

                                <label for="mhs_nim">NIM</label>
                                @foreach($magang as $item)
                                <input class="form-control" type="text" value="{{ $item->mhs_nim }}" readonly>
                                <input type="hidden" name="magang_id" value="{{ $item->magang_id }}">
                                @endforeach

                                <label for="dosen_nip">Nama Dosen</label>
                                    <select name="dosen_nip" class="input select2" id="dosen_nip">
                                        @foreach($dosens as $item)
                                        <option value="{{ $item->dosen_nip }}">{{ $item->dosen_nama }} - {{ $item->dosen_nip }}</option>
                                        @endforeach
                                    </select>
                                <div class="card-footer mt-5">
                                <button type="submit" class="btn btn-primary btn-block btn-flat"><i class="fa fa-save" name="data-id" value="{{ $item->magang_id }}"></i>
                                    Simpan</button>
                                </div>
                            </form>
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
@endsection

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Pilih Dosen",
                allowClear: true
            });

        });

        
    </script>
@endpush
