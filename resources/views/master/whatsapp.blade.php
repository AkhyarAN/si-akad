@extends('layouts.app')
@section('content')
@php $title = 'WhatsApp Gateway'; $breadcrumb = 'Master Data / WhatsApp Gateway'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-whatsapp" style="color: #25D366;"></i> WhatsApp Gateway
        </h3>
        <p style="color: var(--text-muted); font-size: 13px;">Kelola notifikasi otomatis dan kirim pesan broadcast</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-error">
    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
</div>
@endif

<div class="grid-2">
    <!-- Panel Pengaturan Gateway -->
    <div class="card">
        <div class="card-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 16px;">
            <div class="card-title"><i class="bi bi-gear-fill"></i> Pengaturan Gateway (Fonnte API)</div>
        </div>
        
        <form method="POST" action="{{ route('master.whatsapp.settings') }}" style="margin-top: 20px;">
            @csrf
            <div class="form-group">
                <label class="form-label">Status Gateway</label>
                <select name="wa_status" class="form-control" style="background: {{ $status === 'active' ? 'rgba(5, 150, 105, 0.1)' : 'rgba(220, 38, 38, 0.1)' }}; color: {{ $status === 'active' ? 'var(--success-light)' : 'var(--danger-light)' }}; font-weight: bold;">
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>🔴 Tidak Aktif (Mati)</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>🟢 Aktif (Jalan)</option>
                </select>
                <small style="color: var(--text-muted); font-size: 11px; margin-top: 4px; display: block;">
                    *Jika aktif, sistem otomatis mengirim WA ke orang tua saat siswa absen atau ada nilai baru.
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Fonnte API Token</label>
                <div style="display: flex; gap: 8px;">
                    <input type="password" name="wa_api_token" id="token_input" class="form-control" value="{{ $token }}" placeholder="Masukkan API Token Anda" style="flex: 1;">
                    <button type="button" class="btn btn-secondary" onclick="toggleToken()">
                        <i class="bi bi-eye" id="token_icon"></i>
                    </button>
                </div>
                <small style="color: var(--text-muted); font-size: 11px; margin-top: 4px; display: block;">
                    *Dapatkan token Anda dari <a href="https://fonnte.com" target="_blank" style="color: var(--primary-light);">Dashboard Fonnte</a>.
                </small>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Pengaturan</button>
            </div>
        </form>
    </div>

    <!-- Panel Pengujian -->
    <div class="card">
        <div class="card-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 16px;">
            <div class="card-title"><i class="bi bi-plug-fill"></i> Uji Coba Pengiriman</div>
        </div>
        
        <form method="POST" action="{{ route('master.whatsapp.test') }}" style="margin-top: 20px;">
            @csrf
            <div class="form-group">
                <label class="form-label">Nomor Tujuan (Gunakan kode negara, ex: 628123456789)</label>
                <input type="text" name="phone" class="form-control" placeholder="628..." required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Pesan Uji Coba</label>
                <textarea name="message" class="form-control" rows="3" required>Halo, ini adalah pesan uji coba dari SIAKAD SMP untuk memastikan WhatsApp Gateway berjalan dengan baik.</textarea>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-success" {{ $status !== 'active' ? 'disabled' : '' }}>
                    <i class="bi bi-send-fill"></i> Kirim Uji Coba
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Panel Broadcast -->
<div class="card" style="margin-top: 24px;">
    <div class="card-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 16px;">
        <div class="card-title"><i class="bi bi-megaphone-fill"></i> Pesan Massal (Broadcast)</div>
        <p class="card-subtitle">Kirim pesan informasi ke seluruh Guru atau Orang Tua sekaligus.</p>
    </div>

    <form method="POST" action="{{ route('master.whatsapp.broadcast') }}" style="margin-top: 20px;">
        @csrf
        <div style="display: grid; grid-template-columns: 300px 1fr; gap: 24px;">
            <div>
                <div class="form-group">
                    <label class="form-label">Target Penerima</label>
                    <select name="target" class="form-control" required>
                        <option value="">-- Pilih Target --</option>
                        <option value="all_parents">Seluruh Orang Tua / Wali Siswa</option>
                        <option value="all_teachers">Seluruh Guru</option>
                    </select>
                </div>
                
                <div class="alert alert-warning" style="font-size: 12px; margin-top: 20px;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <strong>Peringatan!</strong>
                    Gunakan fitur ini dengan bijak. Mengirim ribuan pesan sekaligus dalam satu waktu dapat menyebabkan nomor WhatsApp pengirim diblokir oleh pihak Meta.
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">Isi Pesan Broadcast</label>
                    <textarea name="message" class="form-control" rows="8" placeholder="Ketik pesan informasi Anda di sini..." required></textarea>
                </div>
                
                <div style="text-align: right;">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin mengirim pesan massal ini ke semua target? Proses ini tidak dapat dibatalkan.')" {{ $status !== 'active' ? 'disabled' : '' }}>
                        <i class="bi bi-send-check-fill"></i> Kirim Broadcast Sekarang
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
function toggleToken() {
    const input = document.getElementById('token_input');
    const icon = document.getElementById('token_icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
@endsection
