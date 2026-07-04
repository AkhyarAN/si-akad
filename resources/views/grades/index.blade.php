@extends('layouts.app')
@section('content')
@php $title = 'Penilaian'; $breadcrumb = 'Penilaian'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Data Nilai</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Kelola nilai siswa</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('grades.report') }}" class="btn btn-secondary"><i class="bi bi-graph-up"></i> Rekap Nilai</a>
        @if(!auth()->user()->hasRole('kepala_sekolah'))
        <a href="{{ route('grades.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Input Nilai</a>
        @endif
    </div>
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
            <label class="form-label">Mapel</label>
            <select name="subject_id" class="form-control">
                <option value="">Semua</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Jenis</label>
            <select name="type" class="form-control">
                <option value="">Semua</option>
                <option value="catatan_sikap" {{ request('type') == 'catatan_sikap' ? 'selected' : '' }}>Catatan Sikap</option>
                <option value="formatif" {{ request('type') == 'formatif' ? 'selected' : '' }}>Asesmen Formatif</option>
                <option value="sts" {{ request('type') == 'sts' ? 'selected' : '' }}>Sumatif Tengah Semester</option>
                <option value="sas" {{ request('type') == 'sas' ? 'selected' : '' }}>Sumatif Akhir Semester</option>
                <option value="kokurikuler" {{ request('type') == 'kokurikuler' ? 'selected' : '' }}>Kokurikuler</option>
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>No</th><th>Siswa</th><th>Kelas</th><th>Mapel</th><th>Jenis</th><th>Keterangan</th><th>Nilai</th><th>Guru</th></tr>
            </thead>
            <tbody>
                @forelse($grades as $i => $g)
                <tr>
                    <td>{{ $grades->firstItem() + $i }}</td>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $g->student->name }}</td>
                    <td>{{ $g->classRoom->name }}</td>
                    <td>{{ $g->subject->name }}</td>
                    <td><span class="badge bg-primary">{{ $g->type_label }}</span></td>
                    <td style="color: var(--text-muted); font-size: 12px;">{{ $g->description ?? '-' }}</td>
                    <td style="font-weight: 700; color: {{ $g->score >= 75 ? 'var(--success-light)' : 'var(--danger-light)' }};">{{ $g->score }}</td>
                    <td>{{ $g->teacher->name }}</td>
                </tr>
                @empty
                <tr><td colspan="8"><div class="empty-state"><i class="bi bi-journal-x"></i><p>Tidak ada data nilai</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $grades->withQueryString()->links() }}</div>
</div>
@endsection
