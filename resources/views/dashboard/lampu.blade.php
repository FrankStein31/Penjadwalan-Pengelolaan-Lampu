@extends('layouts.app')

@section('content')

<style>
    .hero {
        background: url('https://source.unsplash.com/1600x900/?technology,lightbulb') center/cover;
        color: black;
        padding: 60px 20px;
        text-align: center;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    .card-custom {
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 25px;
        transition: all 0.3s;
    }
    .card-custom:hover {
        transform: translateY(-5px);
        box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
    }
    .lampu-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        height: 300px;
        margin-bottom: 20px;
        background-color: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
    }
    .lampu-svg {
        width: 140px;
        height: 200px;
        position: relative;
        z-index: 2;
        filter: drop-shadow(0 5px 10px rgba(0,0,0,0.3));
    }
    .lampu-bulb {
        fill: #f8f9fa;
        stroke: #dee2e6;
        stroke-width: 1;
        transition: all 0.4s ease-in-out;
    }
    .lampu-filament {
        stroke: #ffc107;
        stroke-width: 0.8;
        fill: none;
        opacity: 0;
        transition: opacity 0.4s ease-in-out;
    }
    .lampu-screw {
        fill: #adb5bd;
        stroke: #868e96;
        stroke-width: 0.5;
    }
    .lampu-rod {
        fill: #495057;
        stroke: #343a40;
        stroke-width: 1;
    }
    .lampu-base {
        fill: #343a40;
        stroke: #212529;
        stroke-width: 1;
        filter: drop-shadow(0 2px 5px rgba(0,0,0,0.5));
    }
    .lampu-highlight {
        fill: white;
        opacity: 0.2;
    }

    /* Efek glow */
    .lampu-glow {
        position: absolute;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.5) 0%, rgba(255, 215, 0, 0) 70%);
        filter: blur(20px);
        opacity: 0;
        transition: opacity 0.4s ease-in-out;
        top: 15px;
        z-index: 1;
    }

    /* Refleksi lantai */
    .lampu-reflection {
        position: absolute;
        width: 60px;
        height: 15px;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 50%;
        bottom: 20px;
        filter: blur(3px);
        transform: translateX(-50%);
        left: 50%;
    }

    /* Efek kecerahan */
    .brightness-1 .lampu-bulb { 
        fill: #fff5c4; 
    }
    .brightness-1 .lampu-filament { 
        opacity: 0.3; 
    }
    .brightness-1 .lampu-glow { 
        opacity: 0.3;
        animation: pulsate 4s infinite;
    }

    .brightness-2 .lampu-bulb { 
        fill: #ffeda0; 
    }
    .brightness-2 .lampu-filament { 
        opacity: 0.7; 
    }
    .brightness-2 .lampu-glow { 
        opacity: 0.6;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.6) 0%, rgba(255, 215, 0, 0) 70%);
    }

    .brightness-3 .lampu-bulb { 
        fill: #ffe066; 
    }
    .brightness-3 .lampu-filament { 
        opacity: 1; 
    }
    .brightness-3 .lampu-glow { 
        opacity: 0.9;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.8) 0%, rgba(255, 255, 255, 0.3) 30%, rgba(255, 215, 0, 0) 70%);
    }

    @keyframes pulsate {
        0% { opacity: 0.2; }
        50% { opacity: 0.4; }
        100% { opacity: 0.2; }
    }
    
    .selected-row {
        background-color: rgba(0, 123, 255, 0.1) !important;
    }
    
    /* Datetime styles */
    .datetime-container {
        background-color: white;
        padding: 10px 15px;
        border-radius: 10px;
        box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
        text-align: right;
        color: #495057;
        margin-bottom: 15px;
        font-size: 1.1rem;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }
    
    .datetime-icon {
        margin-right: 8px;
        color: #0d6efd;
        font-size: 1.2rem;
    }
    
    #current-datetime {
        font-weight: 500;
    }
    
    /* Detail Modal Styles */
    .modal-detail .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
    }
    
    .modal-detail .nav-tabs .nav-link.active {
        font-weight: 600;
        border-bottom: 2px solid #0d6efd;
    }
    
    .detail-stat {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s;
    }
    
    .detail-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .detail-stat-value {
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .detail-stat-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .chart-container {
        height: 300px;
        position: relative;
    }
    
    .status-indicator {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }
    
    .status-mati { background-color: #dc3545; }
    .status-redup { background-color: #6c757d; }
    .status-sedang { background-color: #ffc107; }
    .status-terang { background-color: #0d6efd; }
    
    /* Consumption summary styles */
    .consumption-summary {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .consumption-summary h3 {
        margin-bottom: 20px;
        color: #343a40;
        font-weight: 600;
        position: relative;
        padding-bottom: 10px;
    }
    
    .consumption-summary h3:after {
        content: '';
        position: absolute;
        display: block;
        width: 50px;
        height: 3px;
        background: #0d6efd;
        bottom: 0;
        left: 0;
    }
    
    .consumption-tabs .nav-link {
        color: #495057;
        font-weight: 500;
        padding: 10px 15px;
        border-radius: 0;
        border: none;
    }
    
    .consumption-tabs .nav-link.active {
        color: #0d6efd;
        background-color: transparent;
        border-bottom: 2px solid #0d6efd;
    }
    
    .consumption-chart-container {
        height: 250px;
        margin-top: 15px;
    }
    
    /* Tabel lampu */
    .table {
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    
    .table thead th {
        border-bottom: none;
        background-color: #343a40;
        color: white;
        padding: 12px;
    }
    
    .table tbody tr {
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .table tbody tr:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .table tbody td {
        background-color: #fff;
        vertical-align: middle;
        padding: 12px;
    }
    
    .table tbody td:first-child {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }
    
    .table tbody td:last-child {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    
    /* Brightness control buttons */
    .btn-brightness {
        flex: 1;
        padding: 10px;
        font-weight: 500;
        margin: 0 5px;
        transition: all 0.3s;
    }
    
    .btn-brightness:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .brightness-control {
        margin-top: 15px;
        width: 100%;
    }
    
    .section-title {
        position: relative;
        margin-bottom: 30px;
        padding-bottom: 10px;
        text-align: center;
        font-weight: 600;
    }
    
    .section-title:after {
        content: '';
        position: absolute;
        display: block;
        width: 50px;
        height: 3px;
        background: #0d6efd;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
    }
    
    /* Contoh urutan jadwal */
    .schedule-example {
        background-color: white;
        border-radius: 10px;
        padding: 15px;
        margin-top: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .schedule-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 8px;
        border-radius: 5px;
        transition: all 0.2s;
    }
    
    .schedule-item:hover {
        background-color: #f8f9fa;
    }
    
    .schedule-time {
        font-weight: 600;
        min-width: 80px;
    }
    
    .schedule-action {
        margin-left: 10px;
        padding: 3px 8px;
        border-radius: 4px;
    }
    
    .action-on {
        background-color: rgba(25, 135, 84, 0.2);
        color: #198754;
    }
    
    .action-off {
        background-color: rgba(220, 53, 69, 0.2);
        color: #dc3545;
    }
    
    .action-dim {
        background-color: rgba(108, 117, 125, 0.2);
        color: #6c757d;
    }
</style>

<div class="hero">
    <h2>Kelola Lampu</h2>
    <p>Atur dan pantau penggunaan lampu pintar</p>
</div>

<div class="container">
    <!-- Datetime realtime display -->
    <div class="datetime-container">
        <i class="fa fa-clock datetime-icon"></i>
        <span id="current-datetime">Memuat waktu...</span>
    </div>
    
    <!-- Total consumption summary -->
    <div class="consumption-summary">
        <h3>Grafik Konsumsi Daya Semua Lampu</h3>
        
        <ul class="nav nav-tabs consumption-tabs" id="consumptionTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily-panel" type="button" role="tab">Harian</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="weekly-tab" data-bs-toggle="tab" data-bs-target="#weekly-panel" type="button" role="tab">Mingguan</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly-panel" type="button" role="tab">Bulanan</button>
            </li>
        </ul>
        
        <div class="tab-content" id="consumptionTabsContent">
            <div class="tab-pane fade show active" id="daily-panel" role="tabpanel">
                <div class="consumption-chart-container">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
            <div class="tab-pane fade" id="weekly-panel" role="tabpanel">
                <div class="consumption-chart-container">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>
            <div class="tab-pane fade" id="monthly-panel" role="tabpanel">
                <div class="consumption-chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Area kontrol lampu -->
    <div class="card card-custom p-4">
        <h5 class="section-title mb-4">Pengaturan Kecerahan Lampu</h5>
        
        <div class="row">
            <div class="col-md-6">
                <div class="lampu-container" id="lampu-container">
                    <div class="lampu-glow"></div> <!-- Efek bias cahaya -->
                    <svg class="lampu-svg" viewBox="0 0 100 180">
                        <!-- Bohlam lampu -->
                        <ellipse class="lampu-bulb" cx="50" cy="50" rx="25" ry="30" />
                        
                        <!-- Filamen dalam bohlam -->
                        <path class="lampu-filament" d="M40,50 C45,40 55,60 60,50" />
                        <path class="lampu-filament" d="M40,50 C45,60 55,40 60,50" />
                        
                        <!-- Highlight bohlam -->
                        <ellipse class="lampu-highlight" cx="40" cy="40" rx="8" ry="10" />
                        
                        <!-- Bagian sekrup bohlam -->
                        <rect class="lampu-screw" x="40" y="80" width="20" height="10" rx="2" />
                        <rect class="lampu-screw" x="42" y="90" width="16" height="5" rx="1" />
                        
                        <!-- Tiang lampu -->
                        <rect class="lampu-rod" x="45" y="95" width="10" height="55" />
                        
                        <!-- Alas lampu -->
                        <ellipse class="lampu-base" cx="50" cy="155" rx="25" ry="10" />
                        <ellipse fill="#2c3e50" cx="50" cy="152" rx="20" ry="7" />
                    </svg>
                    
                    <!-- Refleksi di bawah lampu -->
                    <div class="lampu-reflection"></div>
                </div>
            </div>
            
            <div class="col-md-6 d-flex flex-column justify-content-center">
                <!-- Input hidden untuk lampu yang dipilih -->
                <input type="hidden" id="selected-lampu-id" value="">
                
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Atur Tingkat Kecerahan</h6>
                    <div class="brightness-control d-flex">
                        <button type="button" class="btn btn-outline-dark btn-brightness" onclick="setBrightness(0)">Mati</button>
                        <button type="button" class="btn btn-outline-secondary btn-brightness" onclick="setBrightness(1)">Redup</button>
                        <button type="button" class="btn btn-outline-warning btn-brightness" onclick="setBrightness(2)">Sedang</button>
                        <button type="button" class="btn btn-outline-primary btn-brightness" onclick="setBrightness(3)">Terang</button>
                    </div>
                </div>
                
                <div class="alert alert-primary text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    Pilih lampu dari tabel di bawah untuk mengatur kecerahan
                </div>
                
                <div class="d-grid gap-2 mt-2">
                    <a href="{{ route('jadwal.index') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-alt me-2"></i> Atur Jadwal Lampu
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title m-0">Daftar Lampu</h2>
        <button class="btn btn-primary shadow" data-bs-toggle="modal" data-bs-target="#tambahLampuModal">
            <i class="fas fa-plus me-2"></i> Tambah Lampu
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Lampu</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Intensitas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lampu as $l)
                <tr data-id="{{ $l->id }}">
                    <td>{{ $l->id }}</td>
                    <td>{{ $l->nama_lampu }}</td>
                    <td>{{ $l->lokasi }}</td>
                    <td>
                        <span class="badge {{ $l->status ? 'bg-success' : 'bg-danger' }}">
                            {{ $l->status ? 'Hidup' : 'Mati' }}
                        </span>
                    </td>
                    <td><span class="intensitas-info">{{ $l->intensitas }}%</span></td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-info btn-sm select-lampu" 
                                    data-id="{{ $l->id }}" 
                                    data-status="{{ $l->status }}"
                                    data-intensitas="{{ $l->intensitas }}">
                                <i class="fas fa-hand-pointer"></i> Pilih
                            </button>
                            <button class="btn btn-success btn-sm toggle-status" data-id="{{ $l->id }}" data-status="{{ $l->status }}">
                                {{ $l->status ? 'Matikan' : 'Hidupkan' }}
                            </button>
                            <button class="btn btn-primary btn-sm view-detail" 
                                    data-id="{{ $l->id }}"
                                    data-nama="{{ $l->nama_lampu }}"
                                    data-lokasi="{{ $l->lokasi }}"
                                    data-status="{{ $l->status }}"
                                    data-intensitas="{{ $l->intensitas }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detailLampuModal">
                                <i class="fa fa-chart-line"></i> Detail
                            </button>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editLampuModal" 
                                data-id="{{ $l->id }}" 
                                data-nama="{{ $l->nama_lampu }}" 
                                data-lokasi="{{ $l->lokasi }}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form action="{{ route('lampu.destroy', $l->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i> Belum ada data lampu
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Lampu -->
<div class="modal fade" id="tambahLampuModal" tabindex="-1" aria-labelledby="tambahLampuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahLampuModalLabel">Tambah Lampu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('lampu.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nama_lampu" class="form-label">Nama Lampu</label>
                        <input type="text" class="form-control" id="nama_lampu" name="nama_lampu" required>
                    </div>
                    <div class="mb-3">
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <input type="text" class="form-control" id="lokasi" name="lokasi" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Lampu -->
<div class="modal fade" id="editLampuModal" tabindex="-1" aria-labelledby="editLampuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLampuModalLabel">Edit Lampu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editLampuForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_nama_lampu" class="form-label">Nama Lampu</label>
                        <input type="text" class="form-control" id="edit_nama_lampu" name="nama_lampu" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_lokasi" class="form-label">Lokasi</label>
                        <input type="text" class="form-control" id="edit_lokasi" name="lokasi" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Lampu -->
<div class="modal fade modal-lg" id="detailLampuModal" tabindex="-1" aria-labelledby="detailLampuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-detail">
            <div class="modal-header">
                <h5 class="modal-title" id="detailLampuModalLabel">Detail Lampu: <span id="detail-lampu-nama"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Informasi dasar -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="detail-stat">
                            <div class="detail-stat-value" id="detail-status">-</div>
                            <div class="detail-stat-label">Status</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-stat">
                            <div class="detail-stat-value" id="detail-intensitas">-</div>
                            <div class="detail-stat-label">Intensitas</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-stat">
                            <div class="detail-stat-value" id="detail-lokasi">-</div>
                            <div class="detail-stat-label">Lokasi</div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabs untuk berbagai jenis data -->
                <ul class="nav nav-tabs mb-3" id="detailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="penggunaan-tab" data-bs-toggle="tab" data-bs-target="#penggunaan" type="button" role="tab">Penggunaan Daya</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="kondisi-tab" data-bs-toggle="tab" data-bs-target="#kondisi" type="button" role="tab">Riwayat Kondisi</button>
                    </li>
                </ul>
                
                <!-- Tab content -->
                <div class="tab-content" id="detailTabsContent">
                    <div class="tab-pane fade show active" id="penggunaan" role="tabpanel" aria-labelledby="penggunaan-tab">
                        <div class="chart-container">
                            <canvas id="penggunaanChart"></canvas>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kondisi" role="tabpanel" aria-labelledby="kondisi-tab">
                        <div class="mb-3">
                            <div class="d-flex justify-content-center mb-3">
                                <div class="me-3"><span class="status-indicator status-mati"></span> Mati</div>
                                <div class="me-3"><span class="status-indicator status-redup"></span> Redup</div>
                                <div class="me-3"><span class="status-indicator status-sedang"></span> Sedang</div>
                                <div><span class="status-indicator status-terang"></span> Terang</div>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="kondisiChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Variabel global untuk chart
    let dailyChart = null;
    let weeklyChart = null;
    let monthlyChart = null;
    let penggunaanChart = null;
    let kondisiChart = null;
    
    // Fungsi untuk memperbarui waktu secara realtime
    function updateDateTime() {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        document.getElementById('current-datetime').textContent = now.toLocaleDateString('id-ID', options);
    }
    
    // Update waktu setiap detik
    setInterval(updateDateTime, 1000);
    
    // Fungsi untuk menampilkan grafik total konsumsi
    function renderTotalConsumptionCharts() {
        fetch('/api/lampu/total-penggunaan')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                renderDailyChart(data.daily);
                renderWeeklyChart(data.weekly);
                renderMonthlyChart(data.monthly);
            })
            .catch(error => {
                console.error('Error fetching total consumption data:', error);
                
                // Data dummy jika API gagal
                const dummyDaily = {
                    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                    data: [12, 19, 15, 8, 22, 14, 10]
                };
                
                const dummyWeekly = {
                    labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                    data: [65, 82, 73, 91]
                };
                
                const dummyMonthly = {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    data: [210, 250, 190, 230, 270, 240]
                };
                
                renderDailyChart(dummyDaily);
                renderWeeklyChart(dummyWeekly);
                renderMonthlyChart(dummyMonthly);
            });
    }
    
    // Render daily chart
    function renderDailyChart(data) {
        const ctx = document.getElementById('dailyChart').getContext('2d');
        
        if (dailyChart) {
            dailyChart.destroy();
        }
        
        dailyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Konsumsi Daya (Watt)',
                    data: data.data,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Konsumsi Daya Harian (7 Hari Terakhir)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Watt'
                        }
                    }
                }
            }
        });
    }
    
    // Render weekly chart
    function renderWeeklyChart(data) {
        const ctx = document.getElementById('weeklyChart').getContext('2d');
        
        if (weeklyChart) {
            weeklyChart.destroy();
        }
        
        weeklyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Konsumsi Daya (Watt)',
                    data: data.data,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Konsumsi Daya Mingguan (4 Minggu Terakhir)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Watt'
                        }
                    }
                }
            }
        });
    }
    
    // Render monthly chart
    function renderMonthlyChart(data) {
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        
        if (monthlyChart) {
            monthlyChart.destroy();
        }
        
        monthlyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Konsumsi Daya (Watt)',
                    data: data.data,
                    backgroundColor: 'rgba(153, 102, 255, 0.7)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Konsumsi Daya Bulanan (6 Bulan Terakhir)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Watt'
                        }
                    }
                }
            }
        });
    }
    
    // Update waktu saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        updateDateTime();
        renderTotalConsumptionCharts();
        
        // Event listener untuk tombol toggle status
        document.querySelectorAll('.toggle-status').forEach(button => {
            button.addEventListener('click', function() {
                const lampuId = this.dataset.id;
                const currentStatus = parseInt(this.dataset.status);
                const newStatus = currentStatus ? 0 : 1;
                
                // Update selected lampu untuk kontrol intensitas
                const selectedLampu = document.getElementById('selected-lampu-id');
                if (selectedLampu) {
                    selectedLampu.value = lampuId;
                }
                
                // Panggil API untuk update status
                updateLampuIntensitas(lampuId, newStatus, newStatus ? 100 : 0);
                
                // Update tampilan lampu demo
                setBrightness(newStatus ? 3 : 0);
            });
        });
        
        // Tambahkan event listener untuk tombol pilih lampu
        document.querySelectorAll('.select-lampu').forEach(button => {
            button.addEventListener('click', function() {
                const lampuId = this.dataset.id;
                const status = parseInt(this.dataset.status);
                const intensitas = parseInt(this.dataset.intensitas);
                
                // Set lampu yang dipilih
                const selectedLampu = document.getElementById('selected-lampu-id');
                if (selectedLampu) {
                    selectedLampu.value = lampuId;
                }
                
                // Highlight tabel row yang dipilih
                document.querySelectorAll('tr.selected-row').forEach(row => {
                    row.classList.remove('selected-row');
                });
                const row = document.querySelector(`tr[data-id="${lampuId}"]`);
                if (row) {
                    row.classList.add('selected-row');
                }
                
                // Set tampilan intensitas sesuai data lampu
                let brightnessLevel = 0;
                if (status) {
                    if (intensitas <= 30) brightnessLevel = 1;
                    else if (intensitas <= 70) brightnessLevel = 2;
                    else brightnessLevel = 3;
                }
                
                setBrightness(brightnessLevel);
                
                // Tampilkan pesan bahwa lampu telah dipilih
                alert(`Lampu ${row.querySelector('td:nth-child(2)').textContent} telah dipilih. Anda bisa mengatur kecerahannya sekarang.`);
            });
        });

        // Event listener untuk modal edit
        const editModal = document.getElementById('editLampuModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                const lokasi = button.getAttribute('data-lokasi');
                
                const form = this.querySelector('#editLampuForm');
                form.action = `/lampu/${id}`;
                form.querySelector('#edit_nama_lampu').value = nama;
                form.querySelector('#edit_lokasi').value = lokasi;
            });
        }
        
        // Event listener untuk modal detail
        const detailModal = document.getElementById('detailLampuModal');
        if (detailModal) {
            detailModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                const lokasi = button.getAttribute('data-lokasi');
                const status = parseInt(button.getAttribute('data-status'));
                const intensitas = parseInt(button.getAttribute('data-intensitas'));
                
                // Set info dasar
                document.getElementById('detail-lampu-nama').textContent = nama;
                document.getElementById('detail-status').textContent = status ? 'Hidup' : 'Mati';
                document.getElementById('detail-status').className = `detail-stat-value ${status ? 'text-success' : 'text-danger'}`;
                document.getElementById('detail-intensitas').textContent = `${intensitas}%`;
                document.getElementById('detail-lokasi').textContent = lokasi;
                
                // Ambil data dari API untuk diagram
                fetch(`/api/lampu/${id}/statistik`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        renderPenggunaanChart(data.penggunaan);
                        renderKondisiChart(data.kondisi);
                    })
                    .catch(error => {
                        console.error('Error fetching lamp statistics:', error);
                        // Gunakan data dummy jika API gagal
                        const dummyPenggunaan = {
                            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                            data: [5, 7, 3, 8, 6, 2, 4]
                        };
                        const dummyKondisi = {
                            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                            data: {
                                mati: [6, 8, 4, 2, 3, 5, 7],
                                redup: [3, 4, 2, 5, 6, 3, 2],
                                sedang: [2, 3, 5, 4, 2, 1, 3],
                                terang: [9, 7, 5, 8, 6, 3, 4]
                            }
                        };
                        renderPenggunaanChart(dummyPenggunaan);
                        renderKondisiChart(dummyKondisi);
                    });
            });
        }
    });
    
    // Render chart penggunaan daya
    function renderPenggunaanChart(data) {
        const ctx = document.getElementById('penggunaanChart').getContext('2d');
        
        // Hapus chart lama jika ada
        if (penggunaanChart) {
            penggunaanChart.destroy();
        }
        
        penggunaanChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Penggunaan (Watt)',
                    data: data.data,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Penggunaan Daya Harian (Watt)'
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Watt'
                        }
                    }
                }
            }
        });
    }
    
    // Render chart kondisi lampu
    function renderKondisiChart(data) {
        const ctx = document.getElementById('kondisiChart').getContext('2d');
        
        // Hapus chart lama jika ada
        if (kondisiChart) {
            kondisiChart.destroy();
        }
        
        kondisiChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Mati',
                        data: data.data.mati,
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 2,
                        tension: 0.2
                    },
                    {
                        label: 'Redup',
                        data: data.data.redup,
                        backgroundColor: 'rgba(108, 117, 125, 0.2)',
                        borderColor: 'rgba(108, 117, 125, 1)',
                        borderWidth: 2,
                        tension: 0.2
                    },
                    {
                        label: 'Sedang',
                        data: data.data.sedang,
                        backgroundColor: 'rgba(255, 193, 7, 0.2)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 2,
                        tension: 0.2
                    },
                    {
                        label: 'Terang',
                        data: data.data.terang,
                        backgroundColor: 'rgba(13, 110, 253, 0.2)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 2,
                        tension: 0.2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Riwayat Kondisi Lampu (Menit)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Menit'
                        }
                    }
                }
            }
        });
    }
    
    function setBrightness(level) {
        const container = document.getElementById('lampu-container');
        container.classList.remove('brightness-0', 'brightness-1', 'brightness-2', 'brightness-3');
        
        if (level > 0) {
            container.classList.add(`brightness-${level}`);
        }
        
        // Jika ada lampu yang dipilih, update intensitasnya
        const selectedLampu = document.getElementById('selected-lampu-id');
        if (selectedLampu && selectedLampu.value) {
            const lampuId = selectedLampu.value;
            let intensitas = 0;
            
            // Konversi level ke nilai intensitas
            switch(level) {
                case 0: intensitas = 0; break;    // Mati
                case 1: intensitas = 30; break;   // Redup
                case 2: intensitas = 70; break;   // Sedang
                case 3: intensitas = 100; break;  // Terang
            }
            
            // Kirim ke server
            updateLampuIntensitas(lampuId, level > 0 ? 1 : 0, intensitas);
        } else {
            console.log('Belum ada lampu yang dipilih, silakan pilih lampu terlebih dahulu');
            alert('Silakan pilih lampu terlebih dahulu dari tabel');
        }
    }
    
    // Fungsi untuk update status dan intensitas lampu
    function updateLampuIntensitas(lampuId, status, intensitas) {
        // Ambil token CSRF dari meta tag
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/api/lampu/${lampuId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                status: status,
                intensitas: intensitas
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if(data.success) {
                // Update tampilan tabel
                const row = document.querySelector(`tr[data-id="${lampuId}"]`);
                if (row) {
                    const statusBadge = row.querySelector('.badge');
                    const statusBtn = row.querySelector('.toggle-status');
                    const intensitasInfo = row.querySelector('.intensitas-info');
                    const detailBtn = row.querySelector('.view-detail');
                    
                    if (status) {
                        statusBadge.classList.remove('bg-danger');
                        statusBadge.classList.add('bg-success');
                        statusBadge.textContent = 'Hidup';
                        statusBtn.textContent = 'Matikan';
                        statusBtn.dataset.status = 1;
                    } else {
                        statusBadge.classList.remove('bg-success');
                        statusBadge.classList.add('bg-danger');
                        statusBadge.textContent = 'Mati';
                        statusBtn.textContent = 'Hidupkan';
                        statusBtn.dataset.status = 0;
                    }
                    
                    // Update info intensitas
                    if (intensitasInfo) {
                        intensitasInfo.textContent = `${intensitas}%`;
                    }
                    
                    // Update data-intensitas pada tombol select dan detail
                    const selectBtn = row.querySelector('.select-lampu');
                    if (selectBtn) {
                        selectBtn.dataset.intensitas = intensitas;
                        selectBtn.dataset.status = status;
                    }
                    
                    if (detailBtn) {
                        detailBtn.dataset.intensitas = intensitas;
                        detailBtn.dataset.status = status;
                    }
                }
            } else {
                console.error('Error updating lamp:', data.message || 'Unknown error');
                alert('Gagal mengubah status lampu');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengubah status lampu');
        });
    }
</script>

@endsection
