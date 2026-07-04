@extends('layouts.app')
@section('content')
@php $title = 'Kelas Saya'; $breadcrumb = 'Wali Kelas / Kelas Saya'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Rekap Kelas {{ $classRoom->name }}</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Data absensi dan rata-rata nilai siswa perwalian Anda</p>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <form method="GET" class="filter-bar">
        <div class="form-group" style="max-width: 150px;">
            <label class="form-label">Bulan</label>
            <select name="month" class="form-control" onchange="this.form.submit()">
                @for($i=1; $i<=12; $i++)
                    <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="form-group" style="max-width: 150px;">
            <label class="form-label">Tahun</label>
            <select name="year" class="form-control" onchange="this.form.submit()">
                @php $currentYear = date('Y'); @endphp
                @for($i = $currentYear; $i >= $currentYear - 2; $i--)
                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Nama Siswa</th>
                    <th>NIS</th>
                    <th style="text-align: center;">Hadir</th>
                    <th style="text-align: center;">Izin</th>
                    <th style="text-align: center;">Sakit</th>
                    <th style="text-align: center;">Alpha</th>
                    <th style="text-align: center;">Rata-rata Nilai<br><small>(Semua Mapel)</small></th>
                </tr>
            </thead>
            <tbody>
                @forelse($studentsData as $index => $data)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-weight: 600;">{{ $data['student']->name }}</td>
                    <td>{{ $data['student']->nis }}</td>
                    <td style="text-align: center;">
                        <span class="badge bg-success">{{ $data['attendance']['hadir'] ?? 0 }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge bg-info">{{ $data['attendance']['izin'] ?? 0 }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge bg-warning">{{ $data['attendance']['sakit'] ?? 0 }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge bg-danger">{{ $data['attendance']['alpha'] ?? 0 }}</span>
                    </td>
                    <td style="text-align: center;">
                        @php $avg = $data['average_grade']; @endphp
                        <span style="font-size: 16px; font-weight: 700; color: {{ $avg >= 75 ? 'var(--success-light)' : ($avg > 0 ? 'var(--danger-light)' : 'var(--text-muted)') }}">
                            {{ $avg > 0 ? $avg : '-' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 32px 0;">Belum ada data siswa di kelas ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
