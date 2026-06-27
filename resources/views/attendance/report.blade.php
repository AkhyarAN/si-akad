@extends('layouts.app')
@section('content')
@php $title = 'Rekap Absensi'; $breadcrumb = 'Laporan / Rekap Absensi'; @endphp

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-bar-chart-fill" style="color: var(--primary-light);"></i> Rekap Absensi</div>
    </div>

    <form method="GET" class="filter-bar">
        <div class="form-group">
            <label class="form-label">Kelas</label>
            <select name="class_room_id" class="form-control" required>
                <option value="">Pilih Kelas</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_room_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Bulan</label>
            <select name="month" class="form-control" required>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->locale('id')->monthName }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Tahun</label>
            <input type="number" name="year" class="form-control" value="{{ request('year', now()->year) }}" required>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Tampilkan</button>
        </div>
    </form>
</div>

@if($reportData)
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th style="text-align: center;">Hadir</th>
                    <th style="text-align: center;">Izin</th>
                    <th style="text-align: center;">Sakit</th>
                    <th style="text-align: center;">Alpha</th>
                    <th style="text-align: center;">Total</th>
                    <th style="text-align: center;">% Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $i => $r)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $r['student']->name }}</td>
                    <td style="text-align: center; color: var(--success-light);">{{ $r['hadir'] }}</td>
                    <td style="text-align: center; color: var(--accent-light);">{{ $r['izin'] }}</td>
                    <td style="text-align: center; color: var(--warning-light);">{{ $r['sakit'] }}</td>
                    <td style="text-align: center; color: var(--danger-light);">{{ $r['alpha'] }}</td>
                    <td style="text-align: center;">{{ $r['total'] }}</td>
                    <td style="text-align: center;">
                        <span style="font-weight: 700; color: {{ $r['percentage'] >= 80 ? 'var(--success-light)' : ($r['percentage'] >= 60 ? 'var(--warning-light)' : 'var(--danger-light)') }};">
                            {{ $r['percentage'] }}%
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
