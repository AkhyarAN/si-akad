@extends('layouts.app')
@section('content')
@php $title = 'Rekap Nilai'; $breadcrumb = 'Laporan / Rekap Nilai'; @endphp

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-graph-up" style="color: var(--secondary-light);"></i> Rekap Nilai Per Mata Pelajaran</div>
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
            <label class="form-label">Mata Pelajaran</label>
            <select name="subject_id" class="form-control" required>
                <option value="">Pilih Mapel</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Tampilkan</button>
        </div>
    </form>
</div>

@if($reportData)
<div class="card">
    <div class="card-header">
        <div class="card-title">{{ $selectedClass->name }} - {{ $selectedSubject->name }} (KKM: {{ $selectedSubject->kkm }})</div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Nama Siswa</th>
                    <th style="text-align: center;">Tugas</th>
                    <th style="text-align: center;">UH</th>
                    <th style="text-align: center;">UTS</th>
                    <th style="text-align: center;">UAS</th>
                    <th style="text-align: center;">Praktik</th>
                    <th style="text-align: center;">Rata-rata</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $i => $r)
                <tr>
                    <td style="font-weight: 700; color: var(--text-primary);">{{ $i + 1 }}</td>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $r['student']->name }}</td>
                    <td style="text-align: center;">{{ $r['tugas'] }}</td>
                    <td style="text-align: center;">{{ $r['ulangan_harian'] }}</td>
                    <td style="text-align: center;">{{ $r['uts'] }}</td>
                    <td style="text-align: center;">{{ $r['uas'] }}</td>
                    <td style="text-align: center;">{{ $r['praktik'] }}</td>
                    <td style="text-align: center; font-weight: 800; font-size: 15px; color: {{ is_numeric($r['average']) && $r['average'] >= $selectedSubject->kkm ? 'var(--success-light)' : 'var(--danger-light)' }};">
                        {{ $r['average'] }}
                    </td>
                    <td>
                        @if($r['below_kkm'])
                            <span class="badge bg-danger">Di bawah KKM</span>
                        @elseif($r['average'] !== '-')
                            <span class="badge bg-success">Tuntas</span>
                        @else
                            <span class="badge bg-secondary">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
