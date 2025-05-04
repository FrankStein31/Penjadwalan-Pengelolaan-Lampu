@extends('layouts.app')

@section('content')

<style>
    .hero {
        background: url('https://source.unsplash.com/1600x900/?calendar,time') center/cover;
        color: black;
        padding: 60px 20px;
        text-align: center;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    .card-custom {
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    .calendar-container {
        margin-top: 30px;
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
    }
    .calendar-day {
        aspect-ratio: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        border-radius: 5px;
        position: relative;
        background: #f8f9fa;
        padding: 5px;
        min-height: 100px;
    }
    .calendar-day-header {
        font-weight: bold;
        padding: 10px 0;
        text-align: center;
        background: #0d6efd;
        color: white;
        border-radius: 5px;
    }
    .calendar-day-number {
        font-size: 1.1rem;
        font-weight: 500;
        margin-bottom: 5px;
        align-self: flex-start;
    }
    .calendar-day-events {
        display: flex;
        flex-direction: column;
        width: 100%;
        font-size: 0.75rem;
        overflow-y: auto;
        max-height: 80px;
    }
    .calendar-day-event {
        display: flex;
        align-items: center;
        margin-bottom: 2px;
        padding: 2px 4px;
        border-radius: 4px;
        width: 100%;
    }
    .event-on {
        background-color: rgba(25, 135, 84, 0.2);
        border-left: 3px solid #198754;
    }
    .event-off {
        background-color: rgba(220, 53, 69, 0.2);
        border-left: 3px solid #dc3545;
    }
    .frekuensi-option {
        margin-top: 10px;
    }
    .jadwal-grid {
        display: grid;
        grid-template-columns: 100px repeat(7, 1fr);
        gap: 10px;
        margin-top: 20px;
    }
    .jadwal-header {
        font-weight: bold;
        padding: 10px;
        text-align: center;
        background: #0d6efd;
        color: white;
        border-radius: 5px;
    }
    .jadwal-lampu {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #e9ecef;
    }
    .jadwal-cell {
        background: #f8f9fa;
        padding: 8px;
        border-radius: 5px;
        min-height: 80px;
        transition: all 0.2s;
    }
    .jadwal-cell:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .jadwal-entry {
        font-size: 0.8rem;
        margin-bottom: 5px;
        padding: 6px;
        border-radius: 4px;
        transition: all 0.2s;
    }
    .jadwal-entry:hover {
        transform: translateX(2px);
    }
    .jadwal-entry-on {
        background-color: rgba(25, 135, 84, 0.2);
        border-left: 3px solid #198754;
    }
    .jadwal-entry-off {
        background-color: rgba(220, 53, 69, 0.2);
        border-left: 3px solid #dc3545;
    }
    
    .section-title {
        position: relative;
        margin-bottom: 20px;
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
    
    .jadwal-kontainer {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid #0d6efd;
        transition: all 0.3s;
    }
    
    .jadwal-kontainer:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transform: translateY(-3px);
    }
    
    .jadwal-info {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .jadwal-waktu {
        display: inline-block;
        background-color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.9rem;
        margin-right: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .jadwal-nyala {
        border-left: 3px solid #198754;
    }
    
    .jadwal-mati {
        border-left: 3px solid #dc3545;
    }
    
    /* Intensitas badges */
    .badge-intensitas {
        font-size: 0.7rem;
        padding: 2px 5px;
        margin-left: 5px;
        vertical-align: middle;
    }
</style>

<div class="hero">
    <h2>Atur Jadwal Lampu</h2>
    <p>Kelola jadwal nyala dan mati lampu secara otomatis</p>
</div>

<div class="container">
    <div class="card card-custom p-4">
        <h5 class="section-title">Tambah Jadwal Baru</h5>
        
        <form action="{{ route('jadwal.store') }}" method="POST">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="lampu_id" class="form-label fw-bold">Pilih Lampu</label>
                    <select id="lampu_id" name="lampu_id" class="form-select shadow-sm" required>
                        <option value="">Pilih Lampu</option>
                        @foreach($lampu as $l)
                        <option value="{{ $l->id }}">{{ $l->nama_lampu }} ({{ $l->lokasi }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="frekuensi" class="form-label fw-bold">Frekuensi Pengulangan</label>
                    <select id="frekuensi" name="frekuensi" class="form-select shadow-sm">
                        <option value="daily" selected>Harian (setiap hari)</option>
                        <option value="weekly">Mingguan (hari tertentu setiap minggu)</option>
                    </select>
                    <small class="text-muted frekuensi-help" id="help-daily">Jadwal berjalan setiap hari pada waktu yang sama</small>
                    <small class="text-muted frekuensi-help" id="help-weekly">Jadwal berjalan pada hari-hari tertentu setiap minggu</small>
                </div>
                
                <div class="col-md-4" id="hari-container">
                    <label for="hari" class="form-label fw-bold">Hari</label>
                    <select id="hari" name="hari" class="form-select shadow-sm" required>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                        <option value="Setiap Hari">Setiap Hari</option>
                    </select>
                </div>
            </div>
                
            <div class="row g-3 mb-3" id="option-weekly" style="display:none;">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Pilih Hari-hari dalam Seminggu</label>
                    <div class="d-flex flex-wrap gap-3 bg-light p-3 rounded shadow-sm">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="day-1" name="repeat_days[]">
                            <label class="form-check-label" for="day-1">Senin</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="2" id="day-2" name="repeat_days[]">
                            <label class="form-check-label" for="day-2">Selasa</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="3" id="day-3" name="repeat_days[]">
                            <label class="form-check-label" for="day-3">Rabu</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="4" id="day-4" name="repeat_days[]">
                            <label class="form-check-label" for="day-4">Kamis</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="5" id="day-5" name="repeat_days[]">
                            <label class="form-check-label" for="day-5">Jumat</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="6" id="day-6" name="repeat_days[]">
                            <label class="form-check-label" for="day-6">Sabtu</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="0" id="day-0" name="repeat_days[]">
                            <label class="form-check-label" for="day-0">Minggu</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="waktu_nyala" class="form-label fw-bold">Waktu Nyala</label>
                    <input type="time" id="waktu_nyala" name="waktu_nyala" class="form-control shadow-sm" required>
                </div>
                <div class="col-md-6">
                    <label for="waktu_mati" class="form-label fw-bold">Waktu Mati</label>
                    <input type="time" id="waktu_mati" name="waktu_mati" class="form-control shadow-sm" required>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Intensitas Cahaya Lampu</label>
                    <div class="d-flex align-items-center">
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="intensitas" id="intensitas-0" value="0" autocomplete="off">
                            <label class="btn btn-outline-dark" for="intensitas-0">Mati (0%)</label>
                            
                            <input type="radio" class="btn-check" name="intensitas" id="intensitas-30" value="30" autocomplete="off">
                            <label class="btn btn-outline-secondary" for="intensitas-30">Redup (30%)</label>
                            
                            <input type="radio" class="btn-check" name="intensitas" id="intensitas-70" value="70" autocomplete="off" checked>
                            <label class="btn btn-outline-warning" for="intensitas-70">Sedang (70%)</label>
                            
                            <input type="radio" class="btn-check" name="intensitas" id="intensitas-100" value="100" autocomplete="off">
                            <label class="btn btn-outline-primary" for="intensitas-100">Terang (100%)</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-success w-100 py-2 shadow">✔ Simpan Jadwal</button>
        </form>
    </div>
</div>

<!-- Daftar Jadwal Terbaru dengan Visual yang lebih baik -->
<div class="container my-5">
    <h2 class="section-title">Daftar Jadwal Terbaru</h2>
    
    <div class="row">
        @forelse($jadwal->sortByDesc('created_at')->take(5) as $j)
        <div class="col-md-6 mb-3">
            <div class="jadwal-kontainer">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">{{ $j->lampu->nama_lampu }}</h5>
                    <span class="badge bg-primary">{{ $j->hari }}</span>
                </div>
                <div class="jadwal-info">
                    <span class="jadwal-waktu jadwal-nyala">
                        Nyala: {{ substr($j->waktu_nyala, 0, 5) }}
                        @if($j->intensitas == 0)
                            <span class="badge bg-dark badge-intensitas">Mati</span>
                        @elseif($j->intensitas <= 30)
                            <span class="badge bg-secondary badge-intensitas">Redup</span>
                        @elseif($j->intensitas <= 70)
                            <span class="badge bg-warning text-dark badge-intensitas">Sedang</span>
                        @else
                            <span class="badge bg-primary badge-intensitas">Terang</span>
                        @endif
                    </span>
                    <span class="jadwal-waktu jadwal-mati">
                        Mati: {{ substr($j->waktu_mati, 0, 5) }}
                    </span>
                    <span class="jadwal-waktu">
                        @if($j->frekuensi == 'daily')
                            <span class="badge bg-success">Harian</span>
                        @elseif($j->frekuensi == 'weekly')
                            <span class="badge bg-info">Mingguan</span>
                        @endif
                    </span>
                </div>
                <div class="mt-2 d-flex justify-content-end">
                    <button class="btn btn-sm btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editJadwalModal" 
                        data-id="{{ $j->id }}"
                        data-lampu-id="{{ $j->lampu_id }}"
                        data-hari="{{ $j->hari }}"
                        data-waktu-nyala="{{ $j->waktu_nyala }}"
                        data-waktu-mati="{{ $j->waktu_mati }}"
                        data-intensitas="{{ $j->intensitas }}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form action="{{ route('jadwal.destroy', $j->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus jadwal ini?')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-warning">
                Belum ada jadwal yang dibuat. Silakan buat jadwal baru.
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Tampilan Grid Jadwal Per Lampu -->
<div class="container mt-5">
    <h2 class="section-title">Jadwal Per Lampu</h2>
    
    <div class="card card-custom p-4">
        <!-- Grid header -->
        <div class="jadwal-grid">
            <div class="jadwal-header">Lampu</div>
            <div class="jadwal-header">Senin</div>
            <div class="jadwal-header">Selasa</div>
            <div class="jadwal-header">Rabu</div>
            <div class="jadwal-header">Kamis</div>
            <div class="jadwal-header">Jumat</div>
            <div class="jadwal-header">Sabtu</div>
            <div class="jadwal-header">Minggu</div>
            
            <!-- Isi grid untuk setiap lampu -->
            @foreach($lampu as $l)
                <div class="jadwal-lampu">{{ $l->nama_lampu }}</div>
                
                @php
                    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                @endphp
                
                @foreach($days as $day)
                    <div class="jadwal-cell">
                        @php
                            $lampuJadwals = $jadwal->filter(function($j) use ($l, $day) {
                                return $j->lampu_id == $l->id && ($j->hari == $day || $j->hari == 'Setiap Hari');
                            });
                        @endphp
                        
                        @forelse($lampuJadwals as $j)
                            <div class="jadwal-entry jadwal-entry-on">
                                Nyala: {{ substr($j->waktu_nyala, 0, 5) }}
                                @if($j->intensitas == 0)
                                    <span class="badge bg-dark">Mati</span>
                                @elseif($j->intensitas <= 30)
                                    <span class="badge bg-secondary">Redup</span>
                                @elseif($j->intensitas <= 70)
                                    <span class="badge bg-warning">Sedang</span>
                                @else
                                    <span class="badge bg-primary">Terang</span>
                                @endif
                            </div>
                            <div class="jadwal-entry jadwal-entry-off">
                                Mati: {{ substr($j->waktu_mati, 0, 5) }}
                            </div>
                        @empty
                            <small class="text-muted">Tidak ada jadwal</small>
                        @endforelse
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
</div>

<!-- Visualisasi Kalender -->
<div class="container mt-5">
    <h2 class="section-title mb-4">Kalender Jadwal Lampu</h2>
    
    <div class="calendar-container">
        <div class="calendar-header">
            <button class="btn btn-outline-primary" id="prev-month"><i class="fas fa-chevron-left"></i></button>
            <h4 id="calendar-title">Mei 2023</h4>
            <button class="btn btn-outline-primary" id="next-month"><i class="fas fa-chevron-right"></i></button>
        </div>
        
        <div class="calendar-grid">
            <div class="calendar-day-header">Min</div>
            <div class="calendar-day-header">Sen</div>
            <div class="calendar-day-header">Sel</div>
            <div class="calendar-day-header">Rab</div>
            <div class="calendar-day-header">Kam</div>
            <div class="calendar-day-header">Jum</div>
            <div class="calendar-day-header">Sab</div>
            
            <!-- Isi kalender akan dirender dengan JavaScript -->
        </div>
    </div>
</div>

<div class="container mt-5 mb-5">
    <div class="card card-custom p-4">
        <h2 class="section-title">Daftar Semua Jadwal</h2>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Lampu</th>
                        <th>Hari</th>
                        <th>Waktu Nyala</th>
                        <th>Waktu Mati</th>
                        <th>Frekuensi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwal as $j)
                    <tr>
                        <td>{{ $j->lampu->nama_lampu }} ({{ $j->lampu->lokasi }})</td>
                        <td>{{ $j->hari }}</td>
                        <td>{{ $j->waktu_nyala }}
                            @if($j->intensitas == 0)
                                <span class="badge bg-dark">Mati</span>
                            @elseif($j->intensitas <= 30)
                                <span class="badge bg-secondary">Redup</span>
                            @elseif($j->intensitas <= 70)
                                <span class="badge bg-warning">Sedang</span>
                            @else
                                <span class="badge bg-primary">Terang</span>
                            @endif
                        </td>
                        <td>{{ $j->waktu_mati }}</td>
                        <td>
                            @if($j->frekuensi == 'once')
                                Sekali
                            @elseif($j->frekuensi == 'daily')
                                Harian
                            @elseif($j->frekuensi == 'weekly')
                                Mingguan
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editJadwalModal" 
                                    data-id="{{ $j->id }}"
                                    data-lampu-id="{{ $j->lampu_id }}"
                                    data-hari="{{ $j->hari }}"
                                    data-waktu-nyala="{{ $j->waktu_nyala }}"
                                    data-waktu-mati="{{ $j->waktu_mati }}"
                                    data-intensitas="{{ $j->intensitas }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form action="{{ route('jadwal.destroy', $j->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus jadwal ini?')">
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
                                <i class="fas fa-exclamation-triangle me-2"></i> Belum ada jadwal yang dibuat
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit Jadwal -->
<div class="modal fade" id="editJadwalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editJadwalForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="edit_lampu_id" class="form-label">Pilih Lampu</label>
                            <select id="edit_lampu_id" name="lampu_id" class="form-select" required>
                                @foreach($lampu as $l)
                                <option value="{{ $l->id }}">{{ $l->nama_lampu }} ({{ $l->lokasi }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_hari" class="form-label">Hari</label>
                            <select id="edit_hari" name="hari" class="form-select" required>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                                <option value="Setiap Hari">Setiap Hari</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="edit_waktu_nyala" class="form-label">Waktu Nyala</label>
                            <input type="time" id="edit_waktu_nyala" name="waktu_nyala" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_waktu_mati" class="form-label">Waktu Mati</label>
                            <input type="time" id="edit_waktu_mati" name="waktu_mati" class="form-control" required>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Intensitas Cahaya Lampu</label>
                            <div class="d-flex align-items-center">
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="intensitas" id="edit_intensitas-0" value="0" autocomplete="off">
                                    <label class="btn btn-outline-dark" for="edit_intensitas-0">Mati (0%)</label>
                                    
                                    <input type="radio" class="btn-check" name="intensitas" id="edit_intensitas-30" value="30" autocomplete="off">
                                    <label class="btn btn-outline-secondary" for="edit_intensitas-30">Redup (30%)</label>
                                    
                                    <input type="radio" class="btn-check" name="intensitas" id="edit_intensitas-70" value="70" autocomplete="off">
                                    <label class="btn btn-outline-warning" for="edit_intensitas-70">Sedang (70%)</label>
                                    
                                    <input type="radio" class="btn-check" name="intensitas" id="edit_intensitas-100" value="100" autocomplete="off">
                                    <label class="btn btn-outline-primary" for="edit_intensitas-100">Terang (100%)</label>
                                </div>
                            </div>
                        </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sembunyikan semua help text terlebih dahulu
        document.querySelectorAll('.frekuensi-help').forEach(el => {
            el.style.display = 'none';
        });
        
        // Tampilkan help text untuk opsi yang dipilih saat ini
        const currentFrekuensi = document.getElementById('frekuensi').value;
        document.getElementById(`help-${currentFrekuensi}`).style.display = 'block';
        
        // Event listener untuk perubahan frekuensi
        const frekuensiSelect = document.getElementById('frekuensi');
        if (frekuensiSelect) {
            frekuensiSelect.addEventListener('change', function() {
                // Sembunyikan semua help text
                document.querySelectorAll('.frekuensi-help').forEach(el => {
                    el.style.display = 'none';
                });
                
                // Tampilkan help text untuk opsi yang dipilih
                document.getElementById(`help-${this.value}`).style.display = 'block';
                
                // Atur visibilitas elemen form berdasarkan frekuensi
                const weeklyOption = document.getElementById('option-weekly');
                const hariContainer = document.getElementById('hari-container');
                
                if (this.value === 'weekly') {
                    weeklyOption.style.display = 'flex';
                    hariContainer.style.display = 'none';
                } else {
                    weeklyOption.style.display = 'none';
                    hariContainer.style.display = 'block';
                }
                
                // Perbarui label berdasarkan frekuensi
                if (this.value === 'daily') {
                    document.querySelector('label[for="hari"]').textContent = 'Terapkan Untuk';
                    const hariSelect = document.getElementById('hari');
                    hariSelect.innerHTML = '<option value="Setiap Hari" selected>Setiap Hari</option>';
                } else {
                    document.querySelector('label[for="hari"]').textContent = 'Hari';
                    // Kembalikan opsi hari default
                    const hariSelect = document.getElementById('hari');
                    hariSelect.innerHTML = `
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                    `;
                }
            });
            
            // Trigger change event untuk menampilkan setting yang benar saat form dibuka
            frekuensiSelect.dispatchEvent(new Event('change'));
        }
        
        // Event listener untuk modal edit
        const editModal = document.getElementById('editJadwalModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const lampuId = button.getAttribute('data-lampu-id');
                const hari = button.getAttribute('data-hari');
                const waktuNyala = button.getAttribute('data-waktu-nyala');
                const waktuMati = button.getAttribute('data-waktu-mati');
                const intensitas = button.getAttribute('data-intensitas') || '100';
                
                const form = this.querySelector('#editJadwalForm');
                form.action = `/jadwal/${id}`;
                form.querySelector('#edit_lampu_id').value = lampuId;
                form.querySelector('#edit_hari').value = hari;
                form.querySelector('#edit_waktu_nyala').value = waktuNyala;
                form.querySelector('#edit_waktu_mati').value = waktuMati;
                
                // Handle juga pengaturan intensitas lampu
                const intensitasRadio = form.querySelector(`#edit_intensitas-${intensitas}`);
                if (intensitasRadio) {
                    intensitasRadio.checked = true;
                } else {
                    // Default to 70 if value doesn't match
                    form.querySelector('#edit_intensitas-70').checked = true;
                }
            });
        }
        
        // Render kalender
        renderCalendar();
        
        // Event listener untuk navigasi kalender
        document.getElementById('prev-month').addEventListener('click', function() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
        });
        
        document.getElementById('next-month').addEventListener('click', function() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
        });
    });
    
    // Variabel untuk kalender
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    
    // Data jadwal (akan diambil dari PHP)
    const jadwalData = {!! json_encode($jadwal) !!};
    
    // Fungsi untuk render kalender
    function renderCalendar() {
        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const dayMapping = {'Senin': 1, 'Selasa': 2, 'Rabu': 3, 'Kamis': 4, 'Jumat': 5, 'Sabtu': 6, 'Minggu': 0, 'Setiap Hari': -1};
        
        // Set judul kalender
        document.getElementById('calendar-title').textContent = `${monthNames[currentMonth]} ${currentYear}`;
        
        // Dapatkan hari pertama dari bulan dan jumlah hari dalam bulan
        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        
        // Hapus isi kalender yang lama
        const calendarGrid = document.querySelector('.calendar-grid');
        const dayHeaders = calendarGrid.querySelectorAll('.calendar-day-header');
        const dayElements = calendarGrid.querySelectorAll('.calendar-day');
        dayElements.forEach(el => el.remove());
        
        // Tambahkan padding untuk hari pertama
        for (let i = 0; i < firstDay; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day';
            calendarGrid.appendChild(emptyDay);
        }
        
        // Tambahkan semua hari dalam bulan
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            
            const dayNumber = document.createElement('div');
            dayNumber.className = 'calendar-day-number';
            dayNumber.textContent = day;
            dayElement.appendChild(dayNumber);
            
            const eventsContainer = document.createElement('div');
            eventsContainer.className = 'calendar-day-events';
            
            // Cek apakah ada jadwal untuk hari ini
            const currentDayOfWeek = new Date(currentYear, currentMonth, day).getDay();
            
            // Group jadwal berdasarkan lampu
            const jadwalByLampu = {};
            
            jadwalData.forEach(jadwal => {
                // Jika Setiap Hari atau hari yang cocok
                if (jadwal.hari === 'Setiap Hari' || dayMapping[jadwal.hari] === currentDayOfWeek) {
                    if (!jadwalByLampu[jadwal.lampu.nama_lampu]) {
                        jadwalByLampu[jadwal.lampu.nama_lampu] = [];
                    }
                    jadwalByLampu[jadwal.lampu.nama_lampu].push(jadwal);
                }
            });
            
            // Tambahkan jadwal untuk setiap lampu
            for (const lampuNama in jadwalByLampu) {
                jadwalByLampu[lampuNama].forEach(jadwal => {
                    // Event untuk jadwal nyala
                    const eventOn = document.createElement('div');
                    eventOn.className = 'calendar-day-event event-on';
                    
                    // Tambahkan indikator intensitas
                    let intensitasTag = '';
                    if (jadwal.intensitas == 0) {
                        intensitasTag = '<span class="badge bg-dark fw-bold me-1">Mati</span>';
                    } else if (jadwal.intensitas <= 30) {
                        intensitasTag = '<span class="badge bg-secondary fw-bold me-1">Redup</span>';
                    } else if (jadwal.intensitas <= 70) {
                        intensitasTag = '<span class="badge bg-warning fw-bold me-1">Sedang</span>';
                    } else {
                        intensitasTag = '<span class="badge bg-primary fw-bold me-1">Terang</span>';
                    }
                    
                    eventOn.innerHTML = `${lampuNama} nyala: ${jadwal.waktu_nyala.substring(0, 5)} ${intensitasTag}`;
                    eventsContainer.appendChild(eventOn);
                    
                    // Event untuk jadwal mati
                    const eventOff = document.createElement('div');
                    eventOff.className = 'calendar-day-event event-off';
                    eventOff.textContent = `${lampuNama} mati: ${jadwal.waktu_mati.substring(0, 5)}`;
                    eventsContainer.appendChild(eventOff);
                });
            }
            
            dayElement.appendChild(eventsContainer);
            calendarGrid.appendChild(dayElement);
        }
    }
</script>

@endsection
