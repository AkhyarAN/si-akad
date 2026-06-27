@extends('layouts.app')
@section('content')
@php $title = 'Tambah Siswa'; $breadcrumb = 'Siswa / Tambah'; @endphp

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-person-plus-fill" style="color: var(--primary-light);"></i> Tambah Siswa Baru</div>
    </div>

    <form method="POST" action="{{ route('students.store') }}">
        @csrf

        <h4 style="font-size: 14px; font-weight: 700; color: var(--accent-light); margin-bottom: 16px;">Data Siswa</h4>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
            <div class="form-group">
                <label class="form-label">NIS *</label>
                <input type="text" name="nis" class="form-control" required value="{{ old('nis') }}">
            </div>
            <div class="form-group">
                <label class="form-label">NISN</label>
                <input type="text" name="nisn" class="form-control" value="{{ old('nisn') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Jenis Kelamin *</label>
                <select name="gender" class="form-control" required>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tempat Lahir</label>
                <input type="text" name="birth_place" class="form-control" value="{{ old('birth_place') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Agama</label>
                <input type="text" name="religion" class="form-control" value="{{ old('religion') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Kelas</label>
                <select name="class_room_id" class="form-control">
                    <option value="">Pilih Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Alamat</label>
            <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
        </div>

        <hr style="border-color: var(--border-color); margin: 24px 0;">
        <h4 style="font-size: 14px; font-weight: 700; color: var(--accent-light); margin-bottom: 16px;">Data Orang Tua / Wali</h4>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
            <div class="form-group">
                <label class="form-label">Nama Orang Tua</label>
                <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name') }}">
            </div>
            <div class="form-group">
                <label class="form-label">No. HP Orang Tua</label>
                <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone') }}">
            </div>
            <div class="form-group">
                <label class="form-label">No. WhatsApp *</label>
                <input type="text" name="parent_whatsapp" class="form-control" placeholder="628xxx" value="{{ old('parent_whatsapp') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Hubungan</label>
                <select name="parent_relationship" class="form-control">
                    <option value="ayah">Ayah</option>
                    <option value="ibu">Ibu</option>
                    <option value="wali">Wali</option>
                </select>
            </div>
        </div>

        <div style="margin-top: 24px; display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
            <a href="{{ route('students.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
