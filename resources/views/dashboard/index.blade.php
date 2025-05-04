    @extends('layouts.app')

    @section('content')


    <style>
        .hero {
            background: url('https://source.unsplash.com/1600x900/?technology,light') center/cover;
            color: white;
            padding: 60px 20px;
            text-align: center;
            border-radius: 10px;
        }
        .card-custom {
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>

    <div class="hero">
        <h2>Selamat Datang di Smart Lighting</h2>
        <p>Sistem monitoring dan pengendalian lampu pintar</p>
    </div>



    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-custom p-3">
                    <h5>Lampu Aktif</h5>
                    <p class="display-4 text-success">{{ $lampuAktif }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-3">
                    <h5>Lampu Nonaktif</h5>
                    <p class="display-4 text-danger">{{ $lampuNonaktif }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-3">
                    <h5>Total Lampu</h5>
                    <p class="display-4 text-primary">{{ $totalLampu }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom mt-4">
        <div class="card-body">
            <h5 class="card-title">Grafik Penggunaan Lampu</h5>
            <div class="chart-container">
                <canvas id="grafikLampu"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('grafikLampu').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($hariSeminggu) !!},
                datasets: [{
                    label: 'Penggunaan Energi (kWh)',
                    data: {!! json_encode($dataEnergi) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
    <div class="card card-custom mt-4 mb-5">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">Mode Hemat</h5>
                <small class="text-muted">Aktifkan mode hemat energi untuk efisiensi penggunaan listrik.</small>
            </div>
            <div class="form-check form-switch fs-4">
                <input class="form-check-input" type="checkbox" role="switch" id="modeHematSwitch" onchange="toggleModeHemat()">
                <label class="form-check-label" for="modeHematSwitch" id="labelModeHemat">Nonaktif</label>
            </div>
        </div>
    </div>
    
    <script>
        function toggleModeHemat() {
            const isChecked = document.getElementById('modeHematSwitch').checked;
            const label = document.getElementById('labelModeHemat');
    
            if (isChecked) {
                label.textContent = 'Aktif';
                // Tambahkan logic aktifkan mode hemat di sini (misal panggil API atau ubah status lokal)
                console.log("Mode Hemat Diaktifkan");
            } else {
                label.textContent = 'Nonaktif';
                console.log("Mode Hemat Dinonaktifkan");
            }
        }
    </script>
    
    @endsection
