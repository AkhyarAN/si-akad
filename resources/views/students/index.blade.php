@extends('layouts.app')
@section('content')
@php $title = 'Data Siswa'; $breadcrumb = 'Siswa'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Data Siswa</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Kelola data siswa</p>
    </div>
    @if(auth()->user()->hasRole('admin'))
    <a href="{{ route('students.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Siswa</a>
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
@endsection
