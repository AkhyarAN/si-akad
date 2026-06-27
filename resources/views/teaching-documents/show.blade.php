@extends('layouts.app')
@section('content')
@php $title = 'Detail Dokumen'; $breadcrumb = 'Perangkat Mengajar / Detail'; @endphp

<div class="card">
    <div class="card-header">
        <div class="card-title">{{ $teachingDocument->title }}</div>
        <div>{!! $teachingDocument->status_badge !!}</div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
        <div>
            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 8px;">Jenis</p>
            <p style="font-weight: 600;">{{ $teachingDocument->type_label }}</p>
        </div>
        <div>
            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 8px;">Mata Pelajaran</p>
            <p style="font-weight: 600;">{{ $teachingDocument->subject->name }}</p>
        </div>
        <div>
            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 8px;">Guru</p>
            <p style="font-weight: 600;">{{ $teachingDocument->teacher->name }}</p>
        </div>
        <div>
            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 8px;">Ukuran File</p>
            <p style="font-weight: 600;">{{ $teachingDocument->file_size_formatted }}</p>
        </div>
        <div>
            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 8px;">Tanggal Upload</p>
            <p style="font-weight: 600;">{{ $teachingDocument->created_at->format('d/m/Y H:i') }}</p>
        </div>
        @if($teachingDocument->reviewer)
        <div>
            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 8px;">Direview oleh</p>
            <p style="font-weight: 600;">{{ $teachingDocument->reviewer->name }} ({{ $teachingDocument->reviewed_at?->format('d/m/Y') }})</p>
        </div>
        @endif
    </div>

    @if($teachingDocument->description)
    <div style="margin-bottom: 24px;">
        <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 8px;">Deskripsi</p>
        <p>{{ $teachingDocument->description }}</p>
    </div>
    @endif

    @if($teachingDocument->review_notes)
    <div class="alert {{ $teachingDocument->status === 'rejected' ? 'alert-danger' : 'alert-success' }}">
        <i class="bi bi-chat-dots-fill"></i>
        <div>
            <strong>Catatan Review:</strong><br>
            {{ $teachingDocument->review_notes }}
        </div>
    </div>
    @endif

    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="{{ route('teaching-documents.download', $teachingDocument) }}" class="btn btn-primary">
            <i class="bi bi-download"></i> Download
        </a>

        @if($teachingDocument->status === 'draft' && (auth()->id() == $teachingDocument->teacher->user_id || auth()->user()->hasRole('admin')))
        <form method="POST" action="{{ route('teaching-documents.submit', $teachingDocument) }}">
            @csrf @method('PUT')
            <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> Ajukan Review</button>
        </form>
        @endif

        @if($teachingDocument->status === 'submitted' && (auth()->user()->hasRole('kepala_sekolah') || auth()->user()->hasRole('admin')))
        <form method="POST" action="{{ route('teaching-documents.review', $teachingDocument) }}" style="display: flex; gap: 8px;">
            @csrf @method('PUT')
            <input type="text" name="review_notes" class="form-control" placeholder="Catatan review..." style="width: 250px;">
            <button type="submit" name="status" value="approved" class="btn btn-success"><i class="bi bi-check-lg"></i> Setujui</button>
            <button type="submit" name="status" value="rejected" class="btn btn-danger"><i class="bi bi-x-lg"></i> Tolak</button>
        </form>
        @endif

        <a href="{{ route('teaching-documents.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection
