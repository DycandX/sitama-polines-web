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
                    <h1>Form Pendaftaran</h1>
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
                                <a href="{{ route('daftar-magang.index', ) }}" class="btn btn-tool"><i
                                        class="fas fa-arrow-alt-circle-left"></i></a>
                            </div>     
                    </div>
                        <div class="card-body">
                        <div class="daftar">
                            <form action="{{ route('daftar-magang.storeTambah') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <label for="nama">Industri</label>
                                <select id="nama" name="nama_industri" class="input select2" required onchange="checkIfNew(this)">
                                    <option value="">Pilih Nama Industri</option>
                                    @foreach ($dataindustri as $industri)
                                        <option value="{{ $industri->industri_id }}">{{ $industri->nama_industri }}</option>
                                    @endforeach
                                    <option value="new">[Tambahkan Industri Baru]</option>
                                </select>

                                <div id="new-industri-input" style="display:none; margin-top: 20px;">
                                    <label for="new_nama_industri">Nama Industri</label>
                                    <input type="text" id="new_nama_industri" name="new_nama_industri" class="input" />

                                    <label for="kota">Pilih Kota:</label>
                                    <select id="kota" name="kota" class="input select2">
                                        <option value="">Pilih Kota</option>
                                        @foreach ($kota as $item)
                                            <option value="{{ $item->name }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>

                                    <label for="new_nama_industri">Alamat Industri</label>
                                    <input type="text" id="alamat" name="alamat" class="input" />

                                </div>

                                <div class="form-group">
                                    <label>Tanggal Mulai</label>
                                    <input type="date" name="tgl_mulai" class="form-control datepicker" placeholder="Pilih Tanggal Mulai">
                                </div>

                                <div class="form-group">
                                    <label>Tanggal Selesai</label>
                                    <input type="date" name="tgl_selesai" class="form-control datepicker" placeholder="Pilih Tanggal Selesai">
                                </div>

                                @error('tgl_mulai')
                                    <div class="invalid-feedback" role="alert">
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror

                                @error('tgl_selesai')
                                    <div class="invalid-feedback" role="alert">
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info btn-block btn-flat"><i class="fa fa-save"></i>
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
                placeholder: "",
                allowClear: true
            });

        });

        function checkIfNew(selectElement) {
            var newIndustriInput = document.getElementById('new-industri-input');
            if (selectElement.value === 'new') {
                newIndustriInput.style.display = 'block';
                $('#kota').select2({
                    placeholder: "Pilih Kota",
                    allowClear: true
                });
            } else {
                newIndustriInput.style.display = 'none';
            }
        }
    </script>
@endpush
