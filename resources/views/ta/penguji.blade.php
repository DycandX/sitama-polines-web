@extends('layouts.app')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Plotting Tim Penguji</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('bimbingan.index') }}">Home</a></li>
                        <li class="breadcrumb-item active">Plotting Tim Penguji</li>
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
                            <h5 class="card-title m-0">Plotting Tim Penguji</h5>
                            <div class="card-tools">
                                <a href="{{ route('ta.index') }}" class="btn btn-tool"><i class="fas fa-arrow-left"></i></a>
                            </div>
                        </div>
                        <form action="{{ route('ta.updatePenguji', $taSidang->ta_sidang_id) }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="penguji_1">Penguji 1</label>
                                    <select name="penguji_1" id="penguji_1" class="form-control select2">
                                        <option value="">Select Penguji 1</option>
                                        <?php
                                        if (isset($penguji[1]) && isset($penguji[1]->dosen_nip)) {
                                            foreach ($dosenList->where('is_penguji', 1)->values() as $dosen) {
                                                if ($penguji[1]->dosen_nip == $dosen->dosen_nip) {
                                                    echo "<option value='" . $dosen->dosen_nip . "' selected='selected'>" . $dosen->dosen_nama . '</option>';
                                                } else {
                                                    echo "<option value='" . $dosen->dosen_nip . "'>" . $dosen->dosen_nama . '</option>';
                                                }
                                            }
                                        } else {
                                            foreach ($dosenList->where('is_penguji', 1)->values() as $dosen) {
                                                if ($dosenListPenguji[0]->dosen_nip == $dosen->dosen_nip) {
                                                    echo "<option value='" . $dosen->dosen_nip . "' selected='selected'>" . $dosen->dosen_nama . '</option>';
                                                } else {
                                                    echo "<option value='" . $dosen->dosen_nip . "'>" . $dosen->dosen_nama . '</option>';
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="penguji_2">Penguji 2</label>
                                    <select name="penguji_2" id="penguji_2" class="form-control select2">
                                        <option value="">Select Penguji 2</option>
                                        <?php
                                        if (isset($penguji[2]) && isset($penguji[2]->dosen_nip)) {
                                            foreach ($dosenList->where('is_penguji', 1)->values() as $dosen) {
                                                if ($penguji[2]->dosen_nip == $dosen->dosen_nip) {
                                                    echo "<option value='" . $dosen->dosen_nip . "' selected='selected'>" . $dosen->dosen_nama . '</option>';
                                                } else {
                                                    echo "<option value='" . $dosen->dosen_nip . "'>" . $dosen->dosen_nama . '</option>';
                                                }
                                            }
                                        } else {
                                            foreach ($dosenList->where('is_penguji', 1)->values() as $dosen) {
                                                if ($dosenListPenguji[1]->dosen_nip == $dosen->dosen_nip) {
                                                    echo "<option value='" . $dosen->dosen_nip . "' selected='selected'>" . $dosen->dosen_nama . '</option>';
                                                } else {
                                                    echo "<option value='" . $dosen->dosen_nip . "'>" . $dosen->dosen_nama . '</option>';
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="penguji_3">Penguji 3</label>
                                    <select name="penguji_3" id="penguji_3" class="form-control select2">
                                        <option value="">Select Penguji 3</option>
                                        <?php
                                        if (isset($penguji[3]) && isset($penguji[3]->dosen_nip)) {
                                            foreach ($dosenList->where('is_penguji', 1)->values() as $dosen) {
                                                if ($penguji[3]->dosen_nip == $dosen->dosen_nip) {
                                                    echo "<option value='" . $dosen->dosen_nip . "' selected='selected'>" . $dosen->dosen_nama . '</option>';
                                                } else {
                                                    echo "<option value='" . $dosen->dosen_nip . "'>" . $dosen->dosen_nama . '</option>';
                                                }
                                            }
                                        } else {
                                            foreach ($dosenList->where('is_penguji', 1)->values() as $dosen) {
                                                if ($dosenListPenguji[2]->dosen_nip == $dosen->dosen_nip) {
                                                    echo "<option value='" . $dosen->dosen_nip . "' selected='selected'>" . $dosen->dosen_nama . '</option>';
                                                } else {
                                                    echo "<option value='" . $dosen->dosen_nip . "'>" . $dosen->dosen_nama . '</option>';
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="sekretaris">Sekretaris</label>
                                    <select name="sekretaris" id="sekretaris" class="form-control select2">
                                        <option value="">Pilih Sekretaris</option>
                                        <?php
                                        if (isset($taSidang->dosen_nip)) {
                                            foreach ($dosenList->where('is_sekretaris', 1)->values() as $dosen) {
                                                if ($taSidang->dosen_nip == $dosen->dosen_nip) {
                                                    echo "<option value='" . $dosen->dosen_nip . "' selected='selected'>" . $dosen->dosen_nama . '</option>';
                                                } else {
                                                    echo "<option value='" . $dosen->dosen_nip . "'>" . $dosen->dosen_nama . '</option>';
                                                }
                                            }
                                        } else {
                                            foreach ($dosenList->where('is_sekretaris', 1)->values() as $dosen) {
                                                if ($dosenListSekretaris[0]->dosen_nip == $dosen->dosen_nip) {
                                                    echo "<option value='" . $dosen->dosen_nip . "' selected='selected'>" . $dosen->dosen_nama . '</option>';
                                                } else {
                                                    echo "<option value='" . $dosen->dosen_nip . "'>" . $dosen->dosen_nama . '</option>';
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update Penguji</button>
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
