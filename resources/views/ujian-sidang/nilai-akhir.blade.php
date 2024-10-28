<?php
use App\Helpers\NilaiHelper;
?>
<!DOCTYPE html>
<html>

<head>
    <title></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">
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


        * {
            font-size: 14px;
            font-family: 'Tinos', Times, serif
        }

        h1 {
            font-size: 18px;
        }

        h2 {
            font-size: 16px;
        }
    </style>
</head>

<body style="padding: 100px;">
    @php
        $romawi = [0, 'I', 'II', 'III', 'IV', 'V'];
    @endphp
    <table cellpadding="5" cellspacing="0" width="100%" class="main-table">
        <thead>
            <tr class="first-row">
                <td rowspan="4" style="text-align: center; width: 20%;" class="first-col">
                    <img src="https://sitama-elektro.polines.ac.id/dist/img/logo-polines-bw.png" width="100">
                </td>
                <td rowspan="4" style="text-align: center">
                    <b>
                        <h1>
                            LAPORAN HASIL UJIAN<br>
                            {{ strtoupper($jenis) }}
                        </h1>
                    </b>
                </td>
                <td>
                    <b>No. FPM</b>
                </td>
                <td>
                    F.PPd.4.08-L4
                </td>
            </tr>
            <tr>
                <td><b>Revisi</b></td>
                <td>3</td>
            </tr>
            <tr>
                <td><b>Tanggal</b></td>
                <td>21.12.2017</td>
            </tr>
            <tr>
                <td><b>Halaman</b></td>
                <td>1 / 2</td>
            </tr>
        </thead>
    </table>
    <div style="border: 3px solid black; padding: 10px; height: 840px;">
        <div>
            Berdasarkan nilai bimbingan dan nilai ujian {{ strtolower($jenis) }} mahasiswa:
        </div>
        <table style="margin-bottom: 20px;">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>{{ $info_sidang->mhs_nama }}</td>
            </tr>
            <tr>
                <td>NIM/Kelas</td>
                <td>:</td>
                <td>{{ $info_sidang->mhs_nim }}/{{ NilaiHelper::nim2kelas($info_sidang->mhs_nim) }}</td>
            </tr>
            <tr>
                <td>Judul {{ ucwords($jenis) }}</td>
                <td>:</td>
                <td>{{ $info_sidang->judul_final }}</td>
            </tr>
            <tr>
                <td>Ruang Ujian</td>
                <td>:</td>
                <td>{{ $info_sidang->ruangan_nama }}</td>
            </tr>
        </table>
        <div>
            dengan hasil sebagai berikut:
        </div>
        <table>
            <tr>
                <td>a.</td>
                <td>Nilai Bimbingan (dari Form B)</td>
                <td></td>
                <td></td>
            </tr>
            @php
                $i = 1;
            @endphp
            @foreach ($data_nilai as $row)
                <tr>
                    <td></td>
                    <td>{{ $i }}. Nilai Pembimbing {{ $romawi[$i] }}</td>
                    <td>=</td>
                    <td>{{ $row->jml_nilai }}</td>
                </tr>
                @php
                    $i++;
                @endphp
            @endforeach
            <tr>
                <td></td>
                <td>Nilai rata-rata Bimbingan (x)</td>
                <td>=</td>
                <td>{{ $info_sidang->nilai_pembimbing }}</td>
            </tr>
            <tr>
                <td>b.</td>
                <td>Nilai rata-rata ujian (dari Form D)=(y)</td>
                <td>=</td>
                <td>{{ $info_sidang->nilai_penguji }}</td>
            </tr>
            <tr>
                <td>c.</td>
                <td>Nilai Akhir = (x) + (y)</td>
                <td>=</td>
                <td>{{ $info_sidang->nilai_penguji + $info_sidang->nilai_pembimbing }}</td>
            </tr>
            <tr>
                <td>d.</td>
                <td>Hasil Akhir</td>
                <td>=</td>
                <td>
                    @switch($info_sidang->status_lulus)
                        @case(1)
                            Lulus / <s>Lulus dengan Revisi</s> / <s>Tidak Lulus</s>
                        @break

                        @case(2)
                            <s>Lulus</s> / Lulus dengan Revisi / <s>Tidak Lulus</s>
                        @break

                        @case(3)
                            <s>Lulus</s> / <s>Lulus dengan Revisi</s> / Tidak Lulus
                        @break

                        @default
                            Belum melaksanakan sidang.
                    @endswitch
                </td>
            </tr>
        </table>
        <p>
            Laporan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
        </p>
        <table width="100%" style="font-size: 14px;">
            <tr>
                <td style="width: 33%"></td>
                <td style="width: 33%"></td>
                <td style="width: 33%">Semarang, {{ $info_sidang->tgl_sidang }}</td>
            </tr>
            <tr>
                @foreach ($data_nilai_penguji as $row)
                    <td style="width: 33%">
                        Penguji {{ $romawi[$row->urutan] }},
                        <br><br>
                        <img src="https://sitama-elektro.polines.ac.id/dist/img/{{ $row->file_ttd }}" height="80">
                        <br>
                        {{ $row->dosen_nama_singkatan }}<br>
                        NIP. {{ $row->dosen_nip }}
                    </td>
                @endforeach
            </tr>
        </table>
        <table width="100%" style="margin-top: 20px; font-size: 14px;">
            <tr>
                @foreach (collect($data_nilai)->where('urutan', 1) as $row)
                    <td>
                        Ketua,
                        <br>
                        <img src="https://sitama-elektro.polines.ac.id/dist/img/{{ $row->file_ttd }}"
                            style="height: 100px;">
                        <br>
                        {{ $row->dosen_nama }}<br>
                        NIP. {{ $row->dosen_nip }}
                    </td>
                @endforeach
                <td>
                    Sekretaris,
                    <br>
                    <img src="https://sitama-elektro.polines.ac.id/dist/img/{{ $info_sidang->sekre_ttd }}"
                        style="height: 100px;">
                    <br>
                    {{ $info_sidang->sekre_nama }}<br>
                    NIP. {{ $info_sidang->sekre_nip }}
                </td>
            </tr>
        </table>
    </div>


    <div style="page-break-inside: auto"></div>

</body>

</html>
