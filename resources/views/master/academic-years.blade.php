@extends('layouts.app')
@section('content')
@php $title = 'Tahun Ajaran'; $breadcrumb = 'Master Data / Tahun Ajaran'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Tahun Ajaran</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Kelola periode tahun ajaran aktif</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="document.getElementById('modalAdd').classList.add('show')">
        <i class="bi bi-plus-lg"></i> Tambah Tahun Ajaran
    </button>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Tahun Ajaran</th>
                    <th>Semester</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($years as $year)
                <tr>
                    <td style="font-weight: 700; color: var(--text-primary);">{{ $year->year }}</td>
                    <td><span class="badge {{ $year->semester == 'ganjil' ? 'bg-primary' : 'bg-secondary' }}">{{ ucfirst($year->semester) }}</span></td>
                    <td>{{ $year->start_date->format('M Y') }} - {{ $year->end_date->format('M Y') }}</td>
                    <td>
                        @if($year->is_active)
                            <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Aktif</span>
                        @else
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        @endif
                    </td>
                    <td>
                        @if(!$year->is_active)
                        <form method="POST" action="{{ route('master.academic-years.activate', $year) }}">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> Set Aktif</button>
                        </form>
                        @endif
                    </td>
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
            <h3 class="modal-title" style="margin: 0;">Tambah Tahun Ajaran</h3>
            <button type="button" onclick="document.getElementById('modalAdd').classList.remove('show')" style="background: none; border: none; color: var(--text-muted); font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        
        <form method="POST" action="{{ route('master.academic-years.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Tahun Ajaran *</label>
                <input type="text" name="year" class="form-control" placeholder="Contoh: 2024/2025" required>
            </div>
            <div class="form-group">
                <label class="form-label">Semester *</label>
                <select name="semester" class="form-control" required>
                    <option value="ganjil">Ganjil</option>
                    <option value="genap">Genap</option>
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai *</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai *</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; color: var(--text-primary); cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" style="width: 16px; height: 16px;"> Jadikan Tahun Ajaran Aktif
                </label>
            </div>
            
            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalAdd').classList.remove('show')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
