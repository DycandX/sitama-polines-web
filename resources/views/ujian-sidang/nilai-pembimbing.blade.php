<?php
use App\Helpers\NilaiHelper;
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
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
            font-size: 14px;
            font-family: 'Tinos', Times, serif
        }
    </style>
</head>

<body style="padding: 100px;">
    @foreach ($data_nilai as $nilai)
        <table cellpadding="5" cellspacing="0" width="100%" class="main-table">
            <thead>
                <tr class="first-row">
                    <td rowspan="4" style="text-align: center; width: 20%;" class="first-col">
                        <img src="https://sitama-elektro.polines.ac.id/dist/img/logo-polines-bw.png" width="100">
                    </td>
                    <td rowspan="4" style="text-align: center">
                        <b>
                            <h1>
                                NILAI BIMBINGAN {{ strtoupper($jenis) }}
                            </h1>
                        </b>
                    </td>
                    <td>
                        <b>No. FPM</b>
                    </td>
                    <td>
                        F.PPd.4.07-L3
                    </td>
                </tr>
                <tr>
                    <td><b>Revisi</b></td>
                    <td>2</td>
                </tr>
                <tr>
                    <td><b>Tanggal</b></td>
                    <td>21.12.2017</td>
                </tr>
                <tr>
                    <td><b>Halaman</b></td>
                    <td>3 / 4</td>
                </tr>
            </thead>
        </table>
        <div style="border: 3px solid black; padding: 10px; height: 800px;">
            <div style="text-align: center;">
                <h2>NILAI BIMBINGAN {{ strtoupper($jenis) }}</h2>
            </div>
            <p>
                Berdasarkan surat tugas nomor {{ $skData->no_sk }} tanggal {{ $info_sidang->tgl_surat_tugas }},
                Pembimbing
                {{ $nilai->urutan }} telah melaksanakan bimbingan {{ strtolower($jenis) }} mahasiswa:
            </p>
            <table>
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
            </table>
            dengan hasil sebagai berikut:
            <table class="not-main-table" cellspacing='0' cellpadding='5' style="width: 100%;">
                <thead>
                    <tr class="first-row">
                        <th class="first-col">No</th>
                        <th>Unsur yang dinilai</th>
                        <th>Nilai<br>(0 s.d. 100)</th>
                        <th>Bobot</th>
                        <th>Nilai x Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $nilai_akhir = 0;
                    foreach ($nilai->nilai as $row) {
                        echo '<tr>';
                        echo '    <td class="first-col">' . $i++ . '</td>';
                        echo '    <td>' . $row[0] . '</td>';
                        echo '    <td style="text-align:right; width: 15%; padding-right: 15px;">' . $row[2] . '</td>';
                        echo '    <td style="text-align:right; width: 15%; padding-right: 15px;">' . $row[1] . '</td>';
                        echo '    <td style="text-align:right; width: 15%; padding-right: 15px;">' . $row[3] . '</td>';
                        echo '</tr>';
                        $nilai_akhir += $row[3];
                    }
                    echo '<tr>';
                    echo '    <td class="first-col" style="text-align: right" colspan="4">Jumlah (Maksimum 50)</td>';
                    echo '    <td style="text-align:right; width: 15%; padding-right: 15px; font-weight:bold;">' . number_format($nilai_akhir, 2, '.', ',') . '</td>';
                    echo '</tr>';
                    ?>
                </tbody>
            </table>
            Hasil penilaian bimbingan {{ strtolower($jenis) }} ini dapat dipergunakan sebagaimana mestinya.
            <table style="width: 100%; margin-top: 40px;">
                <tr>
                    <td></td>
                    <td width='40%'>
                        Semarang, {{ $info_sidang->tgl_sidang }}<br>
                        Pembimbing {{ $nilai->urutan }},<br>
                        &nbsp;&nbsp;<img src="https://sitama-elektro.polines.ac.id/dist/img/{{ $nilai->file_ttd }}"
                            height="100">
                        <br>
                        {{ $nilai->dosen_nama }}<br>
                        NIP. {{ $nilai->dosen_nip }}
                    </td>
                </tr>
            </table>
        </div>


        <div style="page-break-inside: auto"></div>
    @endforeach

</body>

</html>
