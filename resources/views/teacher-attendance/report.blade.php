@extends('layouts.app')
@section('content')
@php $title = 'Laporan Absensi Guru'; $breadcrumb = 'Laporan / Absen Guru'; @endphp

<div class="card" style="margin-bottom: 24px;">
    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 16px;">Filter Laporan</h3>
    <form method="GET" class="filter-bar">
        <div class="form-group" style="flex: 1;">
            <label class="form-label">Pilih Guru</label>
            <select name="teacher_id" class="form-control" required>
                <option value="all" {{ request('teacher_id') == 'all' ? 'selected' : '' }}>Semua Guru</option>
                @foreach($teachers as $t)
                    <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="width: 150px;">
            <label class="form-label">Bulan</label>
            <select name="month" class="form-control" required>
                @for($i=1; $i<=12; $i++)
                    <option value="{{ $i }}" {{ request('month', date('n')) == $i ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="form-group" style="width: 150px;">
            <label class="form-label">Tahun</label>
            <select name="year" class="form-control" required>
                @php $currentYear = date('Y'); @endphp
                @for($i = $currentYear; $i >= $currentYear - 2; $i--)
                    <option value="{{ $i }}" {{ request('year', $currentYear) == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group" style="display: flex; align-items: flex-end;">
            <button type="submit" class="btn btn-primary" style="height: 42px;">
                <i class="bi bi-search"></i> Tampilkan
            </button>
        </div>
    </form>
</div>

@if(isset($reportData))
<div class="card">
    <div style="margin-bottom: 20px; padding: 16px; background: rgba(52, 211, 153, 0.1); border-radius: 12px; border-left: 4px solid var(--success-light);">
        <h4 style="font-size: 15px; font-weight: 700; color: var(--success-light); margin-bottom: 4px;">Total Kehadiran Mengajar: {{ $reportData->count() }} kali</h4>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Berdasarkan absensi kelas yang dilakukan.</p>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Nama Guru</th>
                    <th>Tanggal & Waktu</th>
                    <th>Kelas</th>
                    <th>Mata Pelajaran</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Bukti Foto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData as $index => $data)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $data->teacher?->name ?? '-' }}</td>
                    <td>
                        <div style="font-weight: 600;">{{ \Carbon\Carbon::parse($data->date)->format('d M Y') }}</div>
                        <div style="font-size: 12px; color: var(--text-muted);">
                            <i class="bi bi-clock"></i> {{ $data->time_in ?? '-' }}
                        </div>
                    </td>
                    <td>{{ $data->classRoom?->name ?? '-' }}</td>
                    <td>
                        <div style="font-weight: 600;">{{ $data->subject?->name ?? '-' }}</div>
                        @if($data->schedule)
                        <div style="font-size: 12px; color: var(--text-muted);">{{ $data->schedule->time_range }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span class="badge bg-success">Hadir Mengajar</span>
                    </td>
                    <td style="text-align: center;">
                        @if($data->photo_in)
                            <a href="{{ Storage::url($data->photo_in) }}" target="_blank">
                                <img src="{{ Storage::url($data->photo_in) }}" alt="Foto" style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
                            </a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 32px 0;">Tidak ada data kehadiran mengajar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
