<?php

function nim2kelas($nim)
{
    $temp = '';
    $depan_jenjang = substr($nim, 0, 1);
    if ($depan_jenjang == '3') {
        $temp .= 'IK-3';
    } elseif ($depan_jenjang == '4') {
        $temp .= 'TI-4';
    }
    $depan_kelas = substr($nim, 5, 1);
    if ($depan_kelas == 0) {
        $temp .= 'A';
    } elseif ($depan_kelas == 1) {
        $temp .= 'B';
    } elseif ($depan_kelas == 2) {
        $temp .= 'C';
    } elseif ($depan_kelas == 3) {
        $temp .= 'D';
    } elseif ($depan_kelas == 4) {
        $temp .= 'E';
    } elseif ($depan_kelas == 5) {
        $temp .= 'F';
    } elseif ($depan_kelas == 6) {
        $temp .= 'G';
    } elseif ($depan_kelas == 7) {
        $temp .= 'H';
    }

    return $temp;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title></title>
    <style>
        table.main-table,
        table.not-main-table {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        table.main-table td.first-col {
            border-left: 3px solid black !important;
        }

        table.main-table tr.first-row td {
            border-top: 3px solid black !important;
        }

        table.main-table td,
        table.main-table th {
            border-right: 3px solid black;
            border-bottom: 3px solid black;
        }

        table.not-main-table td.first-col,
        table.not-main-table th.first-col {
            border-left: 1px solid black !important;
        }

        table.not-main-table tr.first-row td,
        table.not-main-table tr.first-row th {
            border-top: 1px solid black !important;
        }

        table.not-main-table td,
        table.not-main-table th {
            border-right: 1px solid black;
            border-bottom: 1px solid black;
        }

        h1 {
            font-size: 18px;
        }

        h2 {
            font-size: 16px;
        }

        * {
            font-size: 16px;
        }
    </style>
</head>

<body style="padding: 100px; font-family: sans-serif;">
    <table cellpadding="5" cellspacing="0" width="100%" class="main-table">
        <thead>
            <tr class="first-row">
                <td rowspan="4" style="text-align: center; width: 20%;" class="first-col">
                    <!-- <img src="https://sitama-elektro.polines.ac.id/dist/img/logo-polines-bw.png" width="100"> -->
                    <img src="{{ asset('dist/img/logo-polines-bw.png') }}" width="100">
                </td>
                <td rowspan="4" style="text-align: center">
                    <h1>
                        SURAT KETERANGAN SELESAI
                        <br>
                        BIMBINGAN {{ strtoupper($jenis[$prodi_id]) }}
                    </h1>
                </td>
                <td>
                    <b>No. FPM</b>
                </td>
                <td>
                    7.5.18/L2
                </td>
            </tr>
            <tr>
                <td><b>Revisi</b></td>
                <td>2</td>
            </tr>
            <tr>
                <td><b>Tanggal</b></td>
                <td>1 Juli 2010</td>
            </tr>
            <tr>
                <td><b>Halaman</b></td>
                <td>1/2</td>
            </tr>
        </thead>
    </table>
    <div style="border: 3px solid black; padding: 10px; height: 800px;">
        <table cellpadding="5" cellspacing="0" width="100%" class="main-table" style="text-align: center;">
            <thead>
                <tr class="first-row">
                    <td class="first-col" style="width: 30%;">
                        <h2>JURUSAN<br>TEKNIK ELEKTRO</h2>
                    </td>
                    <td>
                        <h2>SURAT KETERANGAN SELESAI<br>BIMBINGAN {{ strtoupper($jenis[$prodi_id]) }}</h2>
                    </td>
                </tr>
            </thead>
        </table>
        Kepada<br>
        Yth. Ketua Program Studi {{ $prodi_nama }}<br>
        Politeknik Negeri Semarang<br>
        <br>
        Yang bertanda tangan di bawah ini, Pembimbing I dan Pembimbing II menerangkan bahwa:
        <table cellpadding="5" cellspacing="0" width="100%" class="not-main-table">
            <thead>
                <tr class="first-row">
                    <th class="first-col">No</th>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Kelas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mahasiswa as $row)
                    <tr>
                        <td style="text-align: center;" class="first-col">{{ $loop->iteration }}</td>
                        <td>{{ ucwords(strtolower($row->mhs_nama)) }}</td>
                        <td style="text-align: center;">{{ $row->mhs_nim }}</td>
                        <td style="text-align: center;">{{ nim2kelas($row->mhs_nim) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        Dengan judul {{ strtolower($jenis[$prodi_id]) }}:<br>
        <br>
        <div style="text-align: center; font-weight: bold; line-height: 150%">
            "{{ $judul_ta }}"
        </div>
        <br>
        Benar-benar telah menyelesaikan pembuatan {{ strtolower($jenis[$prodi_id]) }} dan siap untuk melakukan ujian
        wawancara
        {{ strtolower($jenis[$prodi_id]) }}.
        <br>
        <br>
        <br>
        <br>
        <table cellspacing="0" width="100%">
            <tr>
                <td style="width: 5%">
                </td>
                <td style="width: 45%">
                    <br>
                    Pembimbing I,<br>
                    <br>
                    <!-- <img src="https://sitama-elektro.polines.ac.id/dist/img/{{ $pembimbing[0]['ttd'] }}" -->
                    <img src="{{ asset('dist/img') . '/' . $pembimbing[0]['ttd'] }}"
                        height="72">
                    <br>
                    {{ $pembimbing[0]['nama'] }}<br>
                    NIP. {{ $pembimbing[0]['nip'] }}<br>
                </td>
                <td>
                    Semarang, {{ $tanggal_approve }}<br>
                    Pembimbing II,<br>
                    <br>
                    <!-- <img src="https://sitama-elektro.polines.ac.id/dist/img/{{ $pembimbing[1]['ttd'] }}" -->
                    <img src="{{ asset('dist/img') . '/' . $pembimbing[1]['ttd'] }}"
                        height="72">
                    <br>
                    {{ $pembimbing[1]['nama'] }}<br>
                    NIP. {{ $pembimbing[1]['nip'] }}<br>
                </td>
            </tr>
        </table>
    </div>


    <div style="page-break-inside: auto"></div>


    <table cellpadding="5" cellspacing="0" width="100%" class="main-table">
        <thead>
            <tr class="first-row">
                <td rowspan="4" style="text-align: center; width: 20%;" class="first-col">
                    <!-- <img src="https://sitama-elektro.polines.ac.id/dist/img/logo-polines-bw.png" width="100"> -->
                     <img src="{{ asset('dist/img/logo-polines-bw.png') }}" width="100">
                </td>
                <td rowspan="4" style="text-align: center">
                    <h1>
                        SURAT KETERANGAN SIAP
                        <br>
                        UJIAN {{ strtoupper($jenis[$prodi_id]) }}
                    </h1>
                </td>
                <td>
                    <b>No. FPM</b>
                </td>
                <td>
                    7.5.18/L6
                </td>
            </tr>
            <tr>
                <td><b>Revisi</b></td>
                <td>2</td>
            </tr>
            <tr>
                <td><b>Tanggal</b></td>
                <td>1 Juli 2010</td>
            </tr>
            <tr>
                <td><b>Halaman</b></td>
                <td>1/1</td>
            </tr>
        </thead>
    </table>

    <div style="border: 3px solid black; padding: 10px; height: 800px;">
        <h2 style="text-align: center; margin-bottom: 20px;">SURAT KETERANGAN SIAP<br>UJIAN {{ strtoupper($jenis[$prodi_id]) }}</h2>
        Kepada<br>
        Yth. Ketua Program Studi {{ $prodi_nama }}<br>
        Politeknik Negeri Semarang<br>
        <br>
        Yang bertanda tangan di bawah ini, Pembimbing I dan Pembimbing II menerangkan bahwa:
        <table cellpadding="5" cellspacing="0" width="100%" class="not-main-table">
            <thead>
                <tr class="first-row">
                    <th class="first-col">No</th>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Kelas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mahasiswa as $row)
                    <tr>
                        <td style="text-align: center;" class="first-col">{{ $loop->iteration }}</td>
                        <td>{{ ucwords(strtolower($row->mhs_nama)) }}</td>
                        <td style="text-align: center;">{{ $row->mhs_nim }}</td>
                        <td style="text-align: center;">{{ nim2kelas($row->mhs_nim) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        Dengan judul {{ strtolower($jenis[$prodi_id]) }}:<br>
        <br>
        <div style="text-align: center; font-weight: bold; line-height: 150%">
            "{{ $judul_ta }}"
        </div>
        <br>
        Benar-benar telah menyelesaikan pembuatan {{ strtolower($jenis[$prodi_id]) }} dan siap untuk melakukan ujian
        wawancara {{ strtolower($jenis[$prodi_id]) }}.
        <br>
        <br>
        <br>
        <br>
        <table cellspacing="0" width="100%">
            <tr>
                <td style="width: 5%">
                </td>
                <td style="width: 45%">
                    <br>
                    Pembimbing I,<br>
                    <br>
                    <img src="{{ asset('dist/img') . '/' . $pembimbing[0]['ttd'] }}"
                        height="72">
                    <br>
                    {{ $pembimbing[0]['nama'] }}<br>
                    NIP. {{ $pembimbing[0]['nip'] }}<br>
                </td>
                <td>
                    Semarang, {{ $tanggal_approve }}<br>
                    Pembimbing II,<br>
                    <br>
                    <img src="{{ asset('dist/img') . '/' . $pembimbing[1]['ttd'] }}"
                        height="72">
                    <br>
                    {{ $pembimbing[1]['nama'] }}<br>
                    NIP. {{ $pembimbing[1]['nip'] }}<br>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
