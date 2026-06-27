@extends('layouts.app')
@section('content')
@php $title = 'Data Kelas'; $breadcrumb = 'Master Data / Kelas'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Data Kelas</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Kelola rombongan belajar</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="document.getElementById('modalAdd').classList.add('show')">
        <i class="bi bi-plus-lg"></i> Tambah Kelas
    </button>
</div>

@if(!$activeYear)
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle-fill"></i> Tidak ada tahun ajaran aktif. Silakan set tahun ajaran aktif terlebih dahulu.
</div>
@else
<div class="card">
    <div class="card-header">
        <div class="card-title">Kelas Tahun Ajaran {{ $activeYear->year }} ({{ ucfirst($activeYear->semester) }})</div>
    </div>
    <div class="grid-3">
        @foreach($classes as $class)
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; position: relative;">
            <div style="position: absolute; top: 20px; right: 20px;">
                <span class="badge bg-primary">Kelas {{ $class->grade_level }}</span>
            </div>
            <h4 style="font-size: 24px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px;">{{ $class->name }}</h4>
            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 20px;">
                Wali: <strong style="color: var(--text-primary);">{{ $class->homeroomTeacher?->name ?? 'Belum diset' }}</strong>
            </p>
            
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid var(--border-color);">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-people-fill" style="color: var(--accent-light);"></i>
                    <span style="font-size: 14px; font-weight: 600;">{{ $class->students->count() }} / {{ $class->capacity }}</span>
                </div>
                <a href="{{ route('students.index', ['class_room_id' => $class->id]) }}" class="btn btn-sm btn-secondary">Lihat Siswa</a>
            </div>
        </div>
        @endforeach
    </div>
    @if($classes->isEmpty())
        <div class="empty-state">
            <i class="bi bi-building-x"></i>
            <p>Belum ada kelas untuk tahun ajaran aktif</p>
        </div>
    @endif
</div>
@endif

<!-- Modal Add -->
<div class="modal-backdrop" id="modalAdd">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 class="modal-title" style="margin: 0;">Tambah Kelas Baru</h3>
            <button type="button" onclick="document.getElementById('modalAdd').classList.remove('show')" style="background: none; border: none; color: var(--text-muted); font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        
        <form method="POST" action="{{ route('master.classes.store') }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Tingkat Kelas *</label>
                <select name="grade_level" class="form-control" required>
                    <option value="7">Kelas 7 (VII)</option>
                    <option value="8">Kelas 8 (VIII)</option>
                    <option value="9">Kelas 9 (IX)</option>
                </select>
            </div>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Nama Kelas *</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: VII-A" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kapasitas</label>
                    <input type="number" name="capacity" class="form-control" value="32">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Wali Kelas</label>
                <select name="homeroom_teacher_id" class="form-control">
                    <option value="">Pilih Wali Kelas (Opsional)</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalAdd').classList.remove('show')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
