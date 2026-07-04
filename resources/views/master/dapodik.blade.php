@extends('layouts.app')
@section('content')
@php $title = 'Sinkronisasi Dapodik'; $breadcrumb = 'Master Data / Dapodik'; @endphp

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-gear-fill" style="color: var(--primary-light);"></i> Pengaturan Koneksi Dapodik</div>
    </div>
    
    <div style="padding: 16px; background: rgba(8, 145, 178, 0.1); border-left: 4px solid var(--primary-light); border-radius: 8px; margin-bottom: 20px;">
        <h4 style="font-size: 14px; font-weight: 700; color: var(--primary-light); margin-bottom: 4px;">Informasi Penting</h4>
        <p style="font-size: 12px; color: var(--text-muted); margin: 0;">SIAKAD harus terhubung dengan komputer/server sekolah yang menjalankan aplikasi Dapodik. Dapatkan <strong>Token</strong> melalui menu <em>Pengaturan > Web Service</em> di aplikasi Dapodik sekolah Anda.</p>
    </div>

    <form method="POST" action="{{ route('master.dapodik.save') }}" style="margin-bottom: 16px;">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div class="form-group">
                <label class="form-label">URL / IP Server Dapodik *</label>
                <input type="url" name="dapodik_url" class="form-control" value="{{ $settings['dapodik_url'] }}" placeholder="http://192.168.1.10:5774" required>
            </div>
            <div class="form-group">
                <label class="form-label">NPSN Sekolah *</label>
                <input type="text" name="dapodik_npsn" class="form-control" value="{{ $settings['dapodik_npsn'] }}" required>
            </div>
        </div>
        <div class="form-group" style="margin-bottom: 20px;">
            <label class="form-label">Token API Web Service Dapodik *</label>
            <input type="text" name="dapodik_token" class="form-control" value="{{ $settings['dapodik_token'] }}" placeholder="Masukkan token dapodik yang aktif..." required>
        </div>
        <div style="display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Pengaturan</button>
        </div>
    </form>
    
    <form method="POST" action="{{ route('master.dapodik.test') }}">
        @csrf
        <button type="submit" class="btn btn-secondary"><i class="bi bi-wifi"></i> Test Koneksi</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-cloud-arrow-down-fill" style="color: var(--success-light);"></i> Tarik Data (Sinkronisasi)</div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
        <div style="border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; background: var(--bg-card); text-align: center;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(52, 211, 153, 0.2); color: var(--success-light); display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px;">
                <i class="bi bi-person-badge"></i>
            </div>
            <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 8px;">Tarik Data PTK</h4>
            <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 20px;">Menarik data Guru (PTK) dari Dapodik ke database SIAKAD.</p>
            <form method="POST" action="{{ route('master.dapodik.ptk') }}">
                @csrf
                <button type="submit" class="btn btn-success" style="width: 100%;" onclick="return confirm('Proses ini mungkin membutuhkan waktu beberapa saat. Lanjutkan?')">
                    Tarik PTK
                </button>
            </form>
        </div>

        <div style="border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; background: var(--bg-card); text-align: center;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(56, 189, 248, 0.2); color: var(--secondary-light); display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px;">
                <i class="bi bi-people"></i>
            </div>
            <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 8px;">Tarik Peserta Didik</h4>
            <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 20px;">Menarik seluruh data Peserta Didik (Siswa) aktif.</p>
            <form method="POST" action="{{ route('master.dapodik.pd') }}">
                @csrf
                <button type="submit" class="btn btn-info" style="width: 100%; color: #fff;" onclick="return confirm('Proses ini mungkin membutuhkan waktu beberapa saat. Lanjutkan?')">
                    Tarik Siswa
                </button>
            </form>
        </div>

        <div style="border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; background: var(--bg-card); text-align: center;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245, 158, 11, 0.2); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px;">
                <i class="bi bi-houses"></i>
            </div>
            <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 8px;">Tarik Data Kelas</h4>
            <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 20px;">Menarik data Rombongan Belajar (Kelas) dari Dapodik.</p>
            <form method="POST" action="{{ route('master.dapodik.rombel') }}">
                @csrf
                <button type="submit" class="btn btn-warning" style="width: 100%; color: #fff;" onclick="return confirm('Proses ini akan menarik data kelas. Lanjutkan?')">
                    Tarik Kelas
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
