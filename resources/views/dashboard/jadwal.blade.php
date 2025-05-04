@extends('layouts.app')

@section('content')

<style>
    .hero {
        background: url('https://source.unsplash.com/1600x900/?calendar,time') center/cover;
        color: black;
        padding: 60px 20px;
        text-align: center;
        border-radius: 10px;
    }
    .card-custom {
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="hero">
    <h2>Atur Jadwal Lampu</h2>
    <p>Kelola jadwal nyala dan mati lampu secara otomatis</p>
</div>

<div class="container mt-4">
    <div class="card card-custom p-4">
        <h5>Tambah Jadwal Baru</h5>
        <form action="{{ route('jadwal.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="lampu_id" class="form-label">Pilih Lampu</label>
                    <select id="lampu_id" name="lampu_id" class="form-select" required>
                        <option value="">Pilih Lampu</option>
                        @foreach($lampu as $l)
                        <option value="{{ $l->id }}">{{ $l->nama_lampu }} ({{ $l->lokasi }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="hari" class="form-label">Hari</label>
                    <select id="hari" name="hari" class="form-select" required>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="waktu_nyala" class="form-label">Waktu Nyala</label>
                    <input type="time" id="waktu_nyala" name="waktu_nyala" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label for="waktu_mati" class="form-label">Waktu Mati</label>
                    <input type="time" id="waktu_mati" name="waktu_mati" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success mt-3 w-100 shadow">✔ Simpan Jadwal</button>
        </form>
    </div>
</div>

<div class="container mt-5">
    <h2 class="text-center">Jadwal Lampu</h2>
    <div class="table-responsive">
        <table class="table table-hover mt-3 card-custom">
            <thead class="table-dark">
                <tr>
                    <th>Lampu</th>
                    <th>Hari</th>
                    <th>Waktu Nyala</th>
                    <th>Waktu Mati</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwal as $j)
                <tr>
                    <td>{{ $j->lampu->nama_lampu }} ({{ $j->lampu->lokasi }})</td>
                    <td>{{ $j->hari }}</td>
                    <td>{{ $j->waktu_nyala }}</td>
                    <td>{{ $j->waktu_mati }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editJadwalModal" 
                            data-id="{{ $j->id }}"
                            data-lampu-id="{{ $j->lampu_id }}"
                            data-hari="{{ $j->hari }}"
                            data-waktu-nyala="{{ $j->waktu_nyala }}"
                            data-waktu-mati="{{ $j->waktu_mati }}">
                            Edit
                        </button>
                        <form action="{{ route('jadwal.destroy', $j->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus jadwal ini?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada jadwal yang dibuat</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit Jadwal -->
<div class="modal fade" id="editJadwalModal" tabindex="-1" aria-labelledby="editJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editJadwalModalLabel">Edit Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editJadwalForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_lampu_id" class="form-label">Pilih Lampu</label>
                        <select id="edit_lampu_id" name="lampu_id" class="form-select" required>
                            @foreach($lampu as $l)
                            <option value="{{ $l->id }}">{{ $l->nama_lampu }} ({{ $l->lokasi }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_hari" class="form-label">Hari</label>
                        <select id="edit_hari" name="hari" class="form-select" required>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                            <option value="Minggu">Minggu</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_waktu_nyala" class="form-label">Waktu Nyala</label>
                        <input type="time" id="edit_waktu_nyala" name="waktu_nyala" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_waktu_mati" class="form-label">Waktu Mati</label>
                        <input type="time" id="edit_waktu_mati" name="waktu_mati" class="form-control" required>
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
                
                const form = this.querySelector('#editJadwalForm');
                form.action = `/jadwal/${id}`;
                form.querySelector('#edit_lampu_id').value = lampuId;
                form.querySelector('#edit_hari').value = hari;
                form.querySelector('#edit_waktu_nyala').value = waktuNyala;
                form.querySelector('#edit_waktu_mati').value = waktuMati;
            });
        }
    });
</script>

@endsection
