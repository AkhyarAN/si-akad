@extends('layouts.app')
@section('content')
@php $title = 'Data Guru'; $breadcrumb = 'Master Data / Guru'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Data Guru</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Kelola profil guru dan akses akun</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="document.getElementById('modalAdd').classList.add('show')">
        <i class="bi bi-plus-lg"></i> Tambah Guru
    </button>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>Nama Guru</th>
                    <th>L/P</th>
                    <th>Spesialisasi</th>
                    <th>No. HP</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teachers as $teacher)
                <tr>
                    <td style="font-family: monospace;">{{ $teacher->nip ?? '-' }}</td>
                    <td style="font-weight: 600; color: var(--text-primary);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px; border-radius: 8px;">
                                {{ strtoupper(substr($teacher->name, 0, 2)) }}
                            </div>
                            <div>
                                <div>{{ $teacher->name }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $teacher->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $teacher->gender }}</td>
                    <td><span class="badge bg-info">{{ $teacher->specialization ?? 'Umum' }}</span></td>
                    <td>{{ $teacher->phone ?? '-' }}</td>
                    <td><span class="badge bg-success">Aktif</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add -->
<div class="modal-backdrop" id="modalAdd">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 class="modal-title" style="margin: 0;">Tambah Guru Baru</h3>
            <button type="button" onclick="document.getElementById('modalAdd').classList.remove('show')" style="background: none; border: none; color: var(--text-muted); font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        
        <form method="POST" action="{{ route('master.teachers.store') }}">
            @csrf
            
            <div class="alert alert-info">
                <i class="bi bi-info-circle-fill"></i> Password default akun guru adalah: <strong>password123</strong>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap & Gelar *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">NIP</label>
                    <input type="text" name="nip" class="form-control">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">No. HP</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin *</label>
                    <select name="gender" class="form-control" required>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Spesialisasi Mapel</label>
                    <input type="text" name="specialization" class="form-control" placeholder="Contoh: Matematika">
                </div>
            </div>
            
            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalAdd').classList.remove('show')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
