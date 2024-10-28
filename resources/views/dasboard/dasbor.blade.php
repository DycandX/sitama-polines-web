@extends('layouts.app')

@section('content')


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php
    use App\Models\LaporanMagang;
    use App\Models\Magang;
    use Illuminate\Support\Facades\DB;

    // Ambil tahun akademik yang aktif
    $taAktif = DB::table('master_ta')->where('status', 1)->value('ta');

    if ($taAktif) {
        $totalMahasiswa = DB::table('mahasiswa')->count();

        // Hitung mahasiswa yang sudah daftar magang berdasarkan ta yang aktif
        $sudahMagang = DB::table('magangs')
            ->where('ta', $taAktif)
            ->count();

        // Hitung mahasiswa yang belum daftar magang
        $belumMagang = $totalMahasiswa - $sudahMagang;

        // Hitung jumlah mahasiswa yang telah mengumpulkan laporan dengan tipe '2' berdasarkan ta yang aktif
        $jumlahSudahKumpul = DB::table('laporan_magangs')
            ->where('tipe', 2)
            ->whereIn('magang_id', function($query) use ($taAktif) {
                $query->select('magang_id')
                    ->from('magangs')
                    ->where('ta', $taAktif);
            })
            ->distinct('magang_id')
            ->count('magang_id');

        // Hitung jumlah mahasiswa yang ada di tabel magangs yang belum mengumpulkan laporan berdasarkan ta yang aktif
        $jumlahBelumKumpul = DB::table('magangs')
            ->where('ta', $taAktif)
            ->whereNotIn('magang_id', function($query) {
                $query->select('magang_id')
                    ->from('laporan_magangs')
                    ->where('tipe', 2);
            })
            ->count();
        } else {
            // Jika tidak ada tahun akademik yang aktif, semua nilai dihitung sebagai nol
            $totalMahasiswa = 0;
            $sudahMagang = 0;
            $belumMagang = 0;
            $jumlahSudahKumpul = 0;
            $jumlahBelumKumpul = 0;
        }
    ?>

<!-- Lanjutkan dengan tampilan atau logika lainnya di sini -->

    
<div class="content pt-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header text-center">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>Grafik Magang
                        </h3>
                    </div>
                    <div class="card-body d-flex flex-column align-items-center">
                        <canvas id="myChart"></canvas>
                        <style>
                            #myChart {
                                max-width: 261px !important;
                                height: 261px !important;
                            }
                        </style>
                        <div class="col-md-12 mt-4">
                            <p class="text-center"><strong>Keterangan :</strong></p>
                            <div class="progress-group text-center">
                                Sudah Daftar
                                <span class="float-right"><b>{{ $sudahMagang }}</b>/{{ $totalMahasiswa }}</span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar" style="width: {{ ($sudahMagang / $totalMahasiswa) * 100 }}%; background-color: #89BFE3;"></div>
                                </div>
                            </div>
                            <div class="progress-group text-center mt-2">
                                Belum Daftar
                                <span class="float-right"><b>{{ $belumMagang }}</b>/{{ $totalMahasiswa }}</span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar" style="width: {{ ($belumMagang / $totalMahasiswa) * 100 }}%; background-color: #F0575C;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header text-center">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>Statistik Pengumpulan Laporan
                        </h3>                            
                    </div>
                    <div class="card-body">
                    <canvas id="barChart" style="width: 100%; height: 409px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Pie chart
        const pieCtx = document.getElementById('myChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Sudah Daftar', 'Belum Daftar'],
                datasets: [{
                    label: 'Jumlah',
                    data: [{{ $sudahMagang }}, {{ $belumMagang }}],
                    backgroundColor: ['#89BFE3', '#F0575C'],
                    borderWidth: 1
                }]
            }
        });

        // Bar chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Sudah', 'Belum Mengumpulkan'],
                datasets: [{
                    label: '# of Report',
                    data: [{{ $jumlahSudahKumpul }}, {{ $jumlahBelumKumpul }}],
                    backgroundColor: [
                        'rgba(72, 114, 196, 1)',
                        'rgba(231, 129, 49, 1)',
                    ],
                    borderColor: [
                        'rgba(72, 114, 196, 1)',
                        'rgba(231, 129, 49, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>


    
@endsection
@push('js')
    <script>
        $('.toast').toast('show')
    </script>
@endpush
