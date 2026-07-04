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
                <option value="modul_ajar" {{ request('type') == 'modul_ajar' ? 'selected' : '' }}>Modul Ajar</option>
                <option value="atp" {{ request('type') == 'atp' ? 'selected' : '' }}>ATP</option>
                <option value="prota" {{ request('type') == 'prota' ? 'selected' : '' }}>Prota</option>
                <option value="prosem" {{ request('type') == 'prosem' ? 'selected' : '' }}>Prosem</option>
                <option value="kktp" {{ request('type') == 'kktp' ? 'selected' : '' }}>KKTP</option>
                <option value="lainnya" {{ request('type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                                <button type="button" class="btn btn-sm btn-primary" title="Ubah Status" onclick="document.getElementById('modalStatus{{ $doc->id }}').classList.add('show')">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>

                @if(auth()->user()->hasRole('kepala_sekolah') || auth()->user()->hasRole('admin'))
                <!-- Modal Status {{ $doc->id }} -->
                <div class="modal-backdrop" id="modalStatus{{ $doc->id }}">
                    <div class="modal-content" style="max-width: 500px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                            <h3 class="modal-title" style="margin: 0;">Ubah Status Dokumen</h3>
                            <button type="button" onclick="document.getElementById('modalStatus{{ $doc->id }}').classList.remove('show')" style="background: none; border: none; color: var(--text-muted); font-size: 24px; cursor: pointer;">&times;</button>
                        </div>
                        
                        <form method="POST" action="{{ route('teaching-documents.review', $doc) }}">
                            @csrf @method('PUT')
                            
                            <div class="form-group">
                                <label class="form-label">Status *</label>
                                <select name="status" class="form-control" required>
                                    <option value="draft" {{ $doc->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="submitted" {{ $doc->status == 'submitted' ? 'selected' : '' }}>Diajukan</option>
                                    <option value="approved" {{ $doc->status == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="rejected" {{ $doc->status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Catatan / Pesan (Opsional)</label>
                                <textarea name="review_notes" class="form-control" rows="3" placeholder="Contoh: Tolong perbaiki bagian tujuan pembelajaran...">{{ $doc->review_notes }}</textarea>
                            </div>
                            
                            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalStatus{{ $doc->id }}').classList.remove('show')">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-folder-x"></i><p>Tidak ada dokumen</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $documents->withQueryString()->links() }}</div>
</div>
@endsection
