@extends('layouts.app')

@section('content')

<style>
    .hero {
        background: url('https://source.unsplash.com/1600x900/?technology,lightbulb') center/cover;
        color: black;
        padding: 60px 20px;
        text-align: center;
        border-radius: 10px;
    }
    .card-custom {
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }
    .lampu-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        height: 300px;
        margin-bottom: 20px;
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
        text-align: right;
        color: #495057;
        margin-bottom: 15px;
        font-size: 1.1rem;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }
    
    .datetime-icon {
        margin-right: 5px;
        color: #6c757d;
    }
    
    #current-datetime {
        font-weight: 500;
    }
</style>

<div class="hero">
    <h2>Kelola Lampu</h2>
    <p>Atur dan pantau penggunaan lampu pintar</p>
</div>

<div class="container mt-4">
    <!-- Datetime realtime display -->
    <div class="datetime-container">
        <i class="fa fa-clock datetime-icon"></i>
        <span id="current-datetime">Memuat waktu...</span>
    </div>
    
    <div class="text-center">
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
            
            <!-- Input hidden untuk lampu yang dipilih -->
            <input type="hidden" id="selected-lampu-id" value="">
            <h5 class="mt-2 mb-2">Atur Kecerahan</h5>

            <!-- Tombol Opsi Brightness -->
            <div class="btn-group mt-1" role="group" aria-label="Brightness Options">
                <button type="button" class="btn btn-outline-dark" onclick="setBrightness(0)">Mati</button>
                <button type="button" class="btn btn-outline-secondary" onclick="setBrightness(1)">Redup</button>
                <button type="button" class="btn btn-outline-warning" onclick="setBrightness(2)">Sedang</button>
                <button type="button" class="btn btn-outline-primary" onclick="setBrightness(3)">Terang</button>
            </div>
            <p class="text-muted mt-2">Pilih lampu dari tabel untuk mengatur kecerahannya</p>
        </div>
    </div>
</div>

<div class="container mt-5">
    <h2 class="text-center">Daftar Lampu</h2>
    <div class="d-flex justify-content-end">
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahLampuModal">Tambah Lampu</button>
    </div>
    <div class="table-responsive mt-3">
        <table class="table table-hover card-custom">
            <thead class="table-dark">
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
                        <button class="btn btn-info btn-sm select-lampu" 
                                data-id="{{ $l->id }}" 
                                data-status="{{ $l->status }}"
                                data-intensitas="{{ $l->intensitas }}">
                            Pilih
                        </button>
                        <button class="btn btn-success btn-sm toggle-status" data-id="{{ $l->id }}" data-status="{{ $l->status }}">
                            {{ $l->status ? 'Matikan' : 'Hidupkan' }}
                        </button>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editLampuModal" 
                            data-id="{{ $l->id }}" 
                            data-nama="{{ $l->nama_lampu }}" 
                            data-lokasi="{{ $l->lokasi }}">
                            Edit
                        </button>
                        <form action="{{ route('lampu.destroy', $l->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada data lampu</td>
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

<script>
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
    
    // Update waktu saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        updateDateTime();
        
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
    });
    
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
                    
                    // Update data-intensitas pada tombol select
                    const selectBtn = row.querySelector('.select-lampu');
                    if (selectBtn) {
                        selectBtn.dataset.intensitas = intensitas;
                        selectBtn.dataset.status = status;
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
