@extends('layouts.app')
@section('content')
@php $title = 'Riwayat Mengajar'; $breadcrumb = 'Absensi Guru / Riwayat'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Riwayat Absensi Mengajar Anda</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Riwayat kehadiran Anda di setiap kelas yang Anda ampu.</p>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Tanggal & Waktu</th>
                    <th>Kelas</th>
                    <th>Mata Pelajaran</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Bukti Foto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                <tr>
                    <td>
                        <div style="font-weight: 600;">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</div>
                        <div style="font-size: 12px; color: var(--text-muted);">
                            <i class="bi bi-clock"></i> {{ $att->time_in ?? '-' }}
                        </div>
                    </td>
                    <td>{{ $att->classRoom?->name ?? '-' }}</td>
                    <td>
                        <div style="font-weight: 600;">{{ $att->subject?->name ?? '-' }}</div>
                        @if($att->schedule)
                        <div style="font-size: 12px; color: var(--text-muted);">{{ $att->schedule->time_range }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span class="badge bg-success">Hadir Mengajar</span>
                    </td>
                    <td style="text-align: center;">
                        @if($att->photo_in)
                            <a href="{{ Storage::url($att->photo_in) }}" target="_blank">
                                <img src="{{ Storage::url($att->photo_in) }}" alt="Foto In" style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
                            </a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 32px 0;">Belum ada riwayat mengajar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 20px;">
        {{ $attendances->links() }}
    </div>
</div>
@endsection
