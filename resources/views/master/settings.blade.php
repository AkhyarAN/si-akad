@extends('layouts.app')
@section('content')
@php $title = 'Pengaturan Aplikasi'; $breadcrumb = 'Master Data / Pengaturan Aplikasi'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Pengaturan Aplikasi</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Kelola profil sekolah dan tampilan SIAKAD</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif

<div class="card" style="max-width: 600px;">
    <form method="POST" action="{{ route('master.settings.update') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Nama Aplikasi Utama</label>
            <input type="text" name="app_name" class="form-control" value="{{ $appName }}" placeholder="Contoh: SIAKAD SMPN 1" required>
            <small style="color: var(--text-muted); font-size: 11px; margin-top: 4px; display: block;">
                Ditampilkan di bagian atas menu kiri (Sidebar Brand).
            </small>
        </div>

        <div class="form-group">
            <label class="form-label">Sub-judul Aplikasi</label>
            <input type="text" name="app_subtitle" class="form-control" value="{{ $appSubtitle }}" placeholder="Contoh: Sistem Informasi Akademik Digital">
        </div>

        <div style="margin-top: 32px; text-align: right;">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
        </div>
    </form>
</div>

<div class="card" style="max-width: 600px; margin-top: 24px;">
    <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">Preferensi Tampilan</h4>
    
    <div>
        <label class="form-label" style="display: block; margin-bottom: 8px;">Warna Tema Aplikasi</label>
        <div style="display: flex; gap: 12px; align-items: center;">
            <button onclick="setColorTheme('')" style="width: 36px; height: 36px; border: none; cursor: pointer; background: #3B82F6; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" title="Biru (Default)"></button>
            <button onclick="setColorTheme('theme-color-purple')" style="width: 36px; height: 36px; border: none; cursor: pointer; background: #8B5CF6; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" title="Ungu"></button>
            <button onclick="setColorTheme('theme-color-emerald')" style="width: 36px; height: 36px; border: none; cursor: pointer; background: #10B981; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" title="Hijau Emerald"></button>
            <button onclick="setColorTheme('theme-color-rose')" style="width: 36px; height: 36px; border: none; cursor: pointer; background: #F43F5E; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" title="Merah Rose"></button>
        </div>
    </div>
</div>

@endsection
