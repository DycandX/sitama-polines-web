@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/jquery-clockpicker.min.css">
<style>
    /* Tambahkan border pada input file */
    .custom-file-input {
        border: 1px solid #ced4da;
        border-radius: .25rem;
        padding: .375rem .75rem;
        font-size: 1rem;
        line-height: 1.5;
    }

    /* Ganti tombol "Browse" dengan label kustom */
    .custom-file-label::after {
        content: "Browse";
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Tambah Bimbingan</h4>
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
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="card-title m-0"></h5>
                        <div class="card-tools">
                            <a href="{{ route('jadwal-bimbingan.index') }}" class="btn btn-tool"><i
                                    class="fas fa-arrow-alt-circle-left"></i></a>
                        </div>
                    </div>
                    <form action="{{ route('jadwal-bimbingan.store') }}" method="post"
                        enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="text" name="tgl_kegiatan"
                                    class="form-control datepicker @error('tgl_kegiatan') is-invalid @enderror"
                                    placeholder="Pilih Tanggal">
                                @error('tgl_kegiatan')
                                <div class="invalid-feedback" role="alert">
                                    <span>{{ $message }}</span>
                                </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Kegiatan</label>
                                <input type="text" name="kegiatan"
                                    class="form-control @error('kegiatan') is-invalid @enderror"
                                    placeholder="Kegiatan yang dilakukan">
                                @error('kegiatan')
                                <div class="invalid-feedback" role="alert">
                                    <span>{{ $message }}</span>
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info btn-block btn-flat"><i
                                    class="fa fa-save"></i>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/jquery-clockpicker.min.js"></script>
<script>
    $(function () {
        // Setel tanggal dalam Bahasa Indonesia
        $.datepicker.setDefaults($.datepicker.regional['id']);

        // Inisialisasi datepicker
        $(".datepicker").datepicker({
            dateFormat: "yy-mm-dd", // Format tanggal yang diinginkan
            dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'], // Nama hari dalam Bahasa Indonesia
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], // Nama bulan dalam Bahasa Indonesia
            monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] // Nama bulan singkat dalam Bahasa Indonesia
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('.clockpicker').clockpicker({
            donetext: 'Done', // Text untuk tombol "Selesai"
            autoclose: true // Otomatis menutup picker setelah memilih jam
        });
    });
</script>
@endpush
