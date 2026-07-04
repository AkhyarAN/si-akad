@extends('layouts.app')
@section('content')
@php $title = 'Buat Jadwal Baru'; $breadcrumb = 'Rencana Penilaian / Buat'; @endphp

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h3 class="card-title">Form Rencana Penilaian</h3>
    <p class="card-subtitle" style="margin-bottom: 24px;">Atur jadwal ujian atau tugas baru</p>

    <form action="{{ route('exam-plans.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label">Judul (Contoh: Ulangan Harian 1, Remidi UH 1)</label>
            <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Jenis Penilaian</label>
            <select name="type" class="form-control" required>
                <option value="catatan_sikap" {{ old('type') == 'catatan_sikap' ? 'selected' : '' }}>Catatan Sikap</option>
                <option value="formatif" {{ old('type') == 'formatif' ? 'selected' : '' }}>Asesmen Formatif</option>
                <option value="sts" {{ old('type') == 'sts' ? 'selected' : '' }}>Sumatif Tengah Semester</option>
                <option value="sas" {{ old('type') == 'sas' ? 'selected' : '' }}>Sumatif Akhir Semester</option>
                <option value="kokurikuler" {{ old('type') == 'kokurikuler' ? 'selected' : '' }}>Kokurikuler</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Mata Pelajaran</label>
            <select name="subject_id" class="form-control" required>
                <option value="">-- Pilih Mata Pelajaran --</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Kelas</label>
            <select name="class_room_id" class="form-control" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach($classRooms as $class)
                    <option value="{{ $class->id }}" {{ old('class_room_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Tanggal Pelaksanaan</label>
            <input type="date" name="date" class="form-control" required value="{{ old('date') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi / Materi (Opsional)</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px;">
            <a href="{{ route('exam-plans.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
        </div>
    </form>
</div>
@endsection
