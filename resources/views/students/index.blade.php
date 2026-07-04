@extends('layouts.app')
@section('content')
@php $title = 'Data Siswa'; $breadcrumb = 'Siswa'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Data Siswa</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Kelola data siswa</p>
    </div>
    @if(auth()->user()->hasRole('admin'))
    <div style="display: flex; gap: 8px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('importModal').style.display='flex'">
            <i class="bi bi-file-earmark-excel"></i> Import Data
        </button>
        <a href="{{ route('students.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Siswa</a>
    </div>
    @endif
</div>

<div class="card" style="margin-bottom: 20px;">
    <form method="GET" class="filter-bar">
        <div class="form-group">
            <label class="form-label">Kelas</label>
            <select name="class_room_id" class="form-control">
                <option value="">Semua</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_room_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Cari</label>
            <input type="text" name="search" class="form-control" placeholder="Nama atau NIS..." value="{{ request('search') }}">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>No</th><th>NIS</th><th>Nama</th><th>L/P</th><th>Kelas</th><th>Orang Tua</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($students as $i => $s)
                <tr>
                    <td>{{ $students->firstItem() + $i }}</td>
                    <td style="font-family: monospace;">{{ $s->nis }}</td>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $s->name }}</td>
                    <td><span class="badge {{ $s->gender == 'L' ? 'bg-info' : 'bg-secondary' }}">{{ $s->gender }}</span></td>
                    <td>{{ $s->classRoom?->name ?? '-' }}</td>
                    <td>{{ $s->parent?->name ?? '-' }}</td>
                    <td>
                        <div style="display: flex; gap: 4px;">
                            <a href="{{ route('students.show', $s) }}" class="btn btn-sm btn-secondary"><i class="bi bi-eye"></i></a>
                            @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('students.edit', $s) }}" class="btn btn-sm btn-secondary"><i class="bi bi-pencil"></i></a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-people"></i><p>Tidak ada data siswa</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $students->withQueryString()->links() }}</div>
</div>

<!-- Modal Import -->
<div id="importModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: var(--bg-card); padding: 24px; border-radius: 12px; width: 100%; max-width: 400px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
            <h4 style="margin:0; font-weight: 700;">Import Data Siswa</h4>
            <button type="button" onclick="document.getElementById('importModal').style.display='none'" style="background:none; border:none; cursor:pointer;"><i class="bi bi-x-lg"></i></button>
        </div>
        <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Masukkan ke Kelas (Opsional)</label>
                <select name="class_room_id" class="form-control">
                    <option value="">-- Tanpa Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">File Excel / CSV / Dapodik</label>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                <small style="color: var(--text-muted); display: block; margin-top: 8px;">
                    *Sistem otomatis mendeteksi kolom: NIS, NISN, Nama, L/P, dll. <br>
                    <a href="{{ asset('template_siswa.csv') }}" download style="color: var(--primary-light); text-decoration: none;">
                        <i class="bi bi-download"></i> Download Template CSV
                    </a>
                </small>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('importModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Proses Import</button>
            </div>
        </form>
    </div>
</div>
@endsection
