@extends('layouts.app')

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
        .keterangan {
        margin-top: 20px;
        margin-left: 20px;
        }
        .keterangan font-weight-normal { 
            margin-top: 20px;
        }
    </style>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Validasi Status Mahasiswa</h4>
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
                                <a href="{{ route('bimbingan-dosen-magang.index') }}" class="btn btn-tool"><i
                                        class="fas fa-arrow-alt-circle-left"></i></a>
                            </div>
                        </div>
                        <form action="{{ route('bimbingan.valid') }}" method="post">
                          @csrf
                          @foreach ($validasi as $item)
                            <div class="card-body">
                                <div class="border-top label-span-group">
                                    <label>Nama</label>
                                    <span>: {{ $item->mhs_nama }}</span>
                                </div>
                                <div class="border-top label-span-group">
                                    <label>NIM</label>
                                    <span>: {{ $item->mhs_nim }}</span>
                                </div>  
                                <div class="border-top label-span-group">
                                    <label>Industri</label>
                                    @foreach ($item->nama_industri as $nama_industri)
                                        <span>: {{ $nama_industri }}</span>
                                    @endforeach
                                </div>    
                            </div>
                            <div class="keterangan">
                                <p class="font-weight-bold">Keterangan</p>
                                <p class="font-weight-normal">* 1. Pastikan Mahasiswa yang akan divalidasi sudah menyelesaikan program magang</p>
                                <p class="font-weight-normal">* 2. Simpan perubahan dengan klik button validasi </p>
                                <p class="font-weight-normal">* 3. Status Mahasiswa yang sudah divalidasi tidak bisa diganti</p>
                            </div>
                            <input type="hidden" name="magang_id" value="{{ $item->magang_id }}">
                            <div class="card-footer">
                                <button type="submit" class="btn btn-info btn-block btn-flat validasi-button"><i class="fa fa-save"></i>
                                Validasi</button>
                            </div>
                          @endforeach
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pop-up -->
    <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title font-weight-bold">Informasi!!</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body font-weight-normal">
                    Peringatan validasi hanya dilakukan setelah mahasiswa selesai magang, Pastikan kelengkapan mahasiswa sebelum melanjutkan!!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Lanjut</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
<!-- DataTables  & Plugins -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

<script>
     $(document).ready(function(){
        $('#infoModal').modal('show');
    });
</script>
@endpush

