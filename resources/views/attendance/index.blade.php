@extends('layouts.app')
@section('content')
@php $title = 'Absensi Siswa'; $breadcrumb = 'Absensi'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Data Absensi</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Kelola kehadiran siswa</p>
    </div>
    @if(auth()->user()->hasRole('guru') || auth()->user()->hasRole('wali_kelas') || auth()->user()->hasRole('admin'))
    <a href="{{ route('attendance.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Input Absensi
    </a>
    @endif
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 20px;">
    <form method="GET" class="filter-bar">
        <div class="form-group">
            <label class="form-label">Kelas</label>
            <select name="class_room_id" class="form-control">
                <option value="">Semua Kelas</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_room_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Mata Pelajaran</label>
            <select name="subject_id" class="form-control">
                <option value="">Semua Mapel</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Tanggal</label>
            <input type="date" name="date" class="form-control" value="{{ request('date', today()->format('Y-m-d')) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">Semua</option>
                <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>Mata Pelajaran</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Guru</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $i => $att)
                <tr>
                    <td>{{ $attendances->firstItem() + $i }}</td>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $att->student->name }}</td>
                    <td>{{ $att->classRoom->name }}</td>
                    <td>{{ $att->subject->name }}</td>
                    <td>{{ $att->date->format('d/m/Y') }}</td>
                    <td>{!! $att->status_badge !!}</td>
                    <td>{{ $att->teacher->name }}</td>
                    <td style="color: var(--text-muted); font-size: 12px;">{{ $att->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="bi bi-clipboard-x"></i>
                            <p>Tidak ada data absensi</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $attendances->withQueryString()->links() }}</div>
</div>
@endsection
