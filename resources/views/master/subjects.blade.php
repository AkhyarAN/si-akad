@extends('layouts.app')
@section('content')
@php $title = 'Mata Pelajaran'; $breadcrumb = 'Master Data / Mata Pelajaran'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Mata Pelajaran</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Kelola data mapel dan KKM</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="document.getElementById('modalAdd').classList.add('show')">
        <i class="bi bi-plus-lg"></i> Tambah Mapel
    </button>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Mata Pelajaran</th>
                    <th>Tingkat</th>
                    <th>Jam / Minggu</th>
                    <th>KKM</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subjects as $subject)
                <tr>
                    <td style="font-weight: 600; color: var(--primary-light);">{{ $subject->code }}</td>
                    <td style="font-weight: 700; color: var(--text-primary);">{{ $subject->name }}</td>
                    <td>
                        @if($subject->grade_level == 'all')
                            <span class="badge bg-secondary">Semua Tingkat</span>
                        @else
                            <span class="badge bg-info">Kelas {{ $subject->grade_level }}</span>
                        @endif
                    </td>
                    <td>{{ $subject->hours_per_week }} Jam</td>
                    <td><span style="font-weight: 700; color: var(--success-light);">{{ $subject->kkm }}</span></td>
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
            <h3 class="modal-title" style="margin: 0;">Tambah Mata Pelajaran</h3>
            <button type="button" onclick="document.getElementById('modalAdd').classList.remove('show')" style="background: none; border: none; color: var(--text-muted); font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        
        <form method="POST" action="{{ route('master.subjects.store') }}">
            @csrf
            
            <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Nama Mata Pelajaran *</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Matematika" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kode *</label>
                    <input type="text" name="code" class="form-control" placeholder="Contoh: MTK" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Tingkat Kelas *</label>
                <select name="grade_level" class="form-control" required>
                    <option value="all">Semua Tingkat (7, 8, 9)</option>
                    <option value="7">Hanya Kelas 7</option>
                    <option value="8">Hanya Kelas 8</option>
                    <option value="9">Hanya Kelas 9</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">KKM (Kriteria Ketuntasan Minimal) *</label>
                    <input type="number" name="kkm" class="form-control" min="0" max="100" value="75" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jam per Minggu *</label>
                    <input type="number" name="hours_per_week" class="form-control" min="1" value="2" required>
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
