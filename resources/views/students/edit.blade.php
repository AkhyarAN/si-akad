@extends('layouts.app')
@section('content')
@php $title = 'Edit Siswa'; $breadcrumb = 'Siswa / Edit'; @endphp
<div class="card">
    <div class="card-header"><div class="card-title"><i class="bi bi-pencil-fill" style="color: var(--warning-light);"></i> Edit Data Siswa</div></div>
    <form method="POST" action="{{ route('students.update', $student) }}">
        @csrf @method('PUT')
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
            <div class="form-group"><label class="form-label">NIS *</label><input type="text" name="nis" class="form-control" required value="{{ $student->nis }}"></div>
            <div class="form-group"><label class="form-label">NISN</label><input type="text" name="nisn" class="form-control" value="{{ $student->nisn }}"></div>
            <div class="form-group"><label class="form-label">Nama *</label><input type="text" name="name" class="form-control" required value="{{ $student->name }}"></div>
            <div class="form-group"><label class="form-label">Jenis Kelamin *</label>
                <select name="gender" class="form-control"><option value="L" {{ $student->gender=='L'?'selected':'' }}>Laki-laki</option><option value="P" {{ $student->gender=='P'?'selected':'' }}>Perempuan</option></select>
            </div>
            <div class="form-group"><label class="form-label">Tempat Lahir</label><input type="text" name="birth_place" class="form-control" value="{{ $student->birth_place }}"></div>
            <div class="form-group"><label class="form-label">Tanggal Lahir</label><input type="date" name="birth_date" class="form-control" value="{{ $student->birth_date?->format('Y-m-d') }}"></div>
            <div class="form-group"><label class="form-label">Kelas</label>
                <select name="class_room_id" class="form-control"><option value="">Pilih</option>
                    @foreach($classes as $c)<option value="{{ $c->id }}" {{ $student->class_room_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div class="form-group"><label class="form-label">Orang Tua</label>
                <select name="parent_id" class="form-control"><option value="">Pilih</option>
                    @foreach($parents as $p)<option value="{{ $p->id }}" {{ $student->parent_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>@endforeach
                </select>
            </div>
        </div>
        <div class="form-group"><label class="form-label">Alamat</label><textarea name="address" class="form-control" rows="2">{{ $student->address }}</textarea></div>
        <div style="margin-top: 24px; display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
            <a href="{{ route('students.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
