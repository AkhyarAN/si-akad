@extends('layouts.app')
@section('content')
@php $title = 'Perangkat Mengajar'; $breadcrumb = 'Perangkat Mengajar'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Perangkat Mengajar</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Kelola RPP, Silabus, Prota, Prosem, KKM</p>
    </div>
    @if(auth()->user()->hasRole('guru') || auth()->user()->hasRole('wali_kelas') || auth()->user()->hasRole('admin'))
    <a href="{{ route('teaching-documents.create') }}" class="btn btn-primary"><i class="bi bi-cloud-arrow-up"></i> Upload Dokumen</a>
    @endif
</div>

<div class="card" style="margin-bottom: 20px;">
    <form method="GET" class="filter-bar">
        <div class="form-group">
            <label class="form-label">Jenis</label>
            <select name="type" class="form-control">
                <option value="">Semua</option>
                <option value="rpp" {{ request('type') == 'rpp' ? 'selected' : '' }}>RPP</option>
                <option value="silabus" {{ request('type') == 'silabus' ? 'selected' : '' }}>Silabus</option>
                <option value="prota" {{ request('type') == 'prota' ? 'selected' : '' }}>Prota</option>
                <option value="prosem" {{ request('type') == 'prosem' ? 'selected' : '' }}>Prosem</option>
                <option value="kkm" {{ request('type') == 'kkm' ? 'selected' : '' }}>KKM</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">Semua</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Diajukan</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
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
                <tr><th>Judul</th><th>Jenis</th><th>Mapel</th><th>Guru</th><th>File</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                <tr>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $doc->title }}</td>
                    <td><span class="badge bg-info">{{ $doc->type_label }}</span></td>
                    <td>{{ $doc->subject->name }}</td>
                    <td>{{ $doc->teacher->name }}</td>
                    <td style="font-size: 12px; color: var(--text-muted);">{{ $doc->file_size_formatted }}</td>
                    <td>{!! $doc->status_badge !!}</td>
                    <td>
                        <div style="display: flex; gap: 4px;">
                            <a href="{{ route('teaching-documents.show', $doc) }}" class="btn btn-sm btn-secondary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('teaching-documents.download', $doc) }}" class="btn btn-sm btn-secondary" title="Download">
                                <i class="bi bi-download"></i>
                            </a>
                            @if(auth()->user()->hasRole('kepala_sekolah') || auth()->user()->hasRole('admin'))
                                @if($doc->status === 'submitted')
                                <form method="POST" action="{{ route('teaching-documents.review', $doc) }}" style="display: inline;">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui"><i class="bi bi-check-lg"></i></button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-folder-x"></i><p>Tidak ada dokumen</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $documents->withQueryString()->links() }}</div>
</div>
@endsection
