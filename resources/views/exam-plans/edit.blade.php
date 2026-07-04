@extends('layouts.app')
@section('content')
@php $title = 'Edit Jadwal'; $breadcrumb = 'Rencana Penilaian / Edit'; @endphp

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h3 class="card-title">Edit Rencana Penilaian</h3>
    <p class="card-subtitle" style="margin-bottom: 24px;">Perbarui jadwal ujian atau tugas</p>

    <form action="{{ route('exam-plans.update', $examPlan) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label class="form-label">Judul (Contoh: Ulangan Harian 1, Remidi UH 1)</label>
            <input type="text" name="title" class="form-control" required value="{{ old('title', $examPlan->title) }}">
        </div>

        <div class="form-group">
            <label class="form-label">Jenis Penilaian</label>
            <select name="type" class="form-control" required>
                <option value="catatan_sikap" {{ old('type', $examPlan->type) == 'catatan_sikap' ? 'selected' : '' }}>Catatan Sikap</option>
                <option value="formatif" {{ old('type', $examPlan->type) == 'formatif' ? 'selected' : '' }}>Asesmen Formatif</option>
                <option value="sts" {{ old('type', $examPlan->type) == 'sts' ? 'selected' : '' }}>Sumatif Tengah Semester</option>
                <option value="sas" {{ old('type', $examPlan->type) == 'sas' ? 'selected' : '' }}>Sumatif Akhir Semester</option>
                <option value="kokurikuler" {{ old('type', $examPlan->type) == 'kokurikuler' ? 'selected' : '' }}>Kokurikuler</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Mata Pelajaran</label>
            <select name="subject_id" class="form-control" required>
                <option value="">-- Pilih Mata Pelajaran --</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ old('subject_id', $examPlan->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Kelas</label>
            <select name="class_room_id" class="form-control" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach($classRooms as $class)
                    <option value="{{ $class->id }}" {{ old('class_room_id', $examPlan->class_room_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Tanggal Pelaksanaan</label>
            <input type="date" name="date" class="form-control" required value="{{ old('date', $examPlan->date->format('Y-m-d')) }}">
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi / Materi (Opsional)</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $examPlan->description) }}</textarea>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px;">
            <a href="{{ route('exam-plans.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Update Jadwal</button>
        </div>
    </form>
</div>
@endsection
