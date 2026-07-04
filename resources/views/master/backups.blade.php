@extends('layouts.app')
@section('content')
@php $title = 'Backup & Restore Database'; $breadcrumb = 'Master Data / Backup'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Backup Database</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Kelola file cadangan database SIAKAD</p>
    </div>
    <form method="POST" action="{{ route('master.backups.create') }}">
        @csrf
        <button type="submit" class="btn btn-primary" onclick="return confirm('Proses backup akan berjalan. Lanjutkan?')">
            <i class="bi bi-cloud-plus-fill"></i> Buat Backup Baru
        </button>
    </form>
</div>

<div class="card">
    <div style="padding: 16px; background: rgba(239, 68, 68, 0.1); border-left: 4px solid var(--danger); border-radius: 8px; margin-bottom: 20px;">
        <h4 style="font-size: 14px; font-weight: 700; color: var(--danger); margin-bottom: 4px;">Peringatan Restore</h4>
        <p style="font-size: 12px; color: var(--text-muted); margin: 0;">Proses <strong>Restore</strong> akan menimpa seluruh data SIAKAD saat ini dengan data yang ada di file backup. Pastikan Anda memilih file yang tepat!</p>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nama File</th>
                    <th>Ukuran</th>
                    <th>Tanggal Dibuat</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $backup)
                <tr>
                    <td style="font-family: monospace; font-size: 14px; font-weight: 600; color: var(--text-primary);">
                        <i class="bi bi-file-earmark-code" style="color: var(--primary); margin-right: 8px;"></i>
                        {{ $backup['name'] }}
                    </td>
                    <td style="font-size: 13px;">{{ $backup['size'] }}</td>
                    <td style="font-size: 13px;">{{ $backup['created_at'] }}</td>
                    <td>
                        <div style="display: flex; justify-content: flex-end; gap: 8px;">
                            <a href="{{ route('master.backups.download', $backup['name']) }}" class="btn btn-sm btn-secondary" title="Download">
                                <i class="bi bi-download"></i>
                            </a>
                            
                            <form method="POST" action="{{ route('master.backups.restore', $backup['name']) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-warning" style="color: #fff;" title="Restore Database" onclick="return confirm('PERINGATAN! Seluruh data saat ini akan diganti dengan data dari file {{ $backup['name'] }}. Anda yakin ingin melanjutkan?')">
                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('master.backups.delete', $backup['name']) }}" style="display: inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Backup" onclick="return confirm('Yakin ingin menghapus file backup ini?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <i class="bi bi-database-slash"></i>
                            <p>Belum ada file backup database</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
