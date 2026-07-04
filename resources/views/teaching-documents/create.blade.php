@extends('layouts.app')
@section('content')
@php $title = 'Upload Dokumen'; $breadcrumb = 'Perangkat Mengajar / Upload'; @endphp

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-cloud-arrow-up-fill" style="color: var(--secondary-light);"></i> Upload Perangkat Mengajar</div>
    </div>

    <form method="POST" action="{{ route('teaching-documents.store') }}" enctype="multipart/form-data">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Judul Dokumen *</label>
                <input type="text" name="title" class="form-control" placeholder="Contoh: RPP Matematika Kelas VII" required value="{{ old('title') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Jenis Dokumen *</label>
                <select name="type" class="form-control" required>
                    <option value="">Pilih Jenis Perangkat</option>
                    <option value="modul_ajar">Modul Ajar</option>
                    <option value="atp">Alur Tujuan Pembelajaran (ATP)</option>
                    <option value="prota">Program Tahunan (Prota)</option>
                    <option value="prosem">Program Semester (Prosem)</option>
                    <option value="kktp">Kriteria Ketercapaian (KKTP)</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Mata Pelajaran *</label>
                <select name="subject_id" class="form-control" required>
                    <option value="">Pilih Mapel</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Kelas (Opsional)</label>
                <select name="class_room_id" class="form-control">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi singkat dokumen...">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">File Dokumen * (PDF, DOC, DOCX, XLS, XLSX, PPT - Max 10MB)</label>
            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" required
                   style="padding: 12px;">
        </div>

        <div style="margin-top: 24px; display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary"><i class="bi bi-cloud-arrow-up"></i> Upload Dokumen</button>
            <a href="{{ route('teaching-documents.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
