@extends('layouts.app')
@push('css')
    <style>
        @keyframes moveText {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(calc(-100% - 200px));
            }
        }

        .moving-text-container {
            overflow: hidden;
            width: calc(100% + 200px);
        }

        .moving-text {
            animation: moveText 12s ease-in-out infinite;
            white-space: nowrap;
            display: inline-block;
            font-size: 1.2em;
            text-shadow: 0 0 10px rgba(0, 123, 255, 0.7), 0 0 20px rgba(0, 123, 255, 0.5), 0 0 30px rgba(0, 123, 255, 0.3);
            color: #007bff;
            letter-spacing: 2px;
        }

        /* Style for locked sessions (gray text) */
        .locked-session-date {
            color: gray !important;
        }
    </style>
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Selamat datang {{ ucwords(auth()->user()->name) }}</h4>
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
                            <h5 class="m-0">Jadwal Sidang Tugas Akhir</h5>
                        </div>
                        <div class="card-body">
                            <div id="accordion">
                                @foreach ($jadwal as $key => $value)
                                    @php
                                        // Check if the session is locked
                                        $isLocked = $key <= date('Y-m-d');
                                    @endphp
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title w-100">
                                                <a class="d-block w-100 {{ $isLocked ? 'locked-session-date' : '' }}" data-toggle="collapse"
                                                    href="#collapse{{ $key }}">
                                                    <?php
                                                    $Carbon::setLocale('id');
                                                    echo $Carbon::parse($key)->translatedFormat('l, j F Y');
                                                    ?>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse{{ $key }}" class="collapse" data-parent="#accordion">
                                            <div class="card-body">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2" class="text-center" style="width: 5%">Sesi</th>
                                                            <th colspan="5" class="text-center">Ruang</th>
                                                        </tr>
                                                        <tr>
                                                            @php
                                                                $temp = [];
                                                                foreach ($value as $ke => $val) {
                                                                    foreach ($val as $k => $v) {
                                                                        $temp[] = $k;
                                                                    }
                                                                }
                                                            @endphp

                                                            @foreach ($ruang as $row)
                                                                @if (in_array($row->ruangan_nama, $temp))
                                                                    <td class="text-center" style="width: 19%">
                                                                        {{ $row->ruangan_nama }}</td>
                                                                @endif
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($value as $ke => $val)
                                                            <tr>
                                                                <td class="text-center;">{{ str_replace('Sesi ', '', $ke) }}</td>
                                                                @foreach ($val as $k => $v)
                                                                    @if ($v[0] == null)
                                                                        <td style="vertical-align: middle;">
                                                                            {!! "<div class='text-center'>" .
                                                                                ($isLocked
                                                                                    ? '<span class="badge badge-danger">Terkunci</span>'
                                                                                    : '<span class="badge badge-success">Tersedia</span>') .
                                                                                '</div>' !!}
                                                                        </td>
                                                                    @else
                                                                        <td>
                                                                            <b>Judul TA/Skripsi:</b><br>
                                                                            {!! $v[0] !!}
                                                                            <br><br>
                                                                            <b>Mahasiswa:</b><br>
                                                                            {!! $v[1] !!}
                                                                            <br><br>
                                                                            <b>Pembimbing:</b><br>
                                                                            {!! $v[2] !!}
                                                                            <br><br>
                                                                            <b>Penguji:</b><br>
                                                                            {!! $v[3] !!}
                                                                            <br><br>
                                                                            <b>Sekretaris:</b><br>
                                                                            {!! $v[4] !!}
                                                                        </td>
                                                                    @endif
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
