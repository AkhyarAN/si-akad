@extends('layouts.app')
@section('content')
@php $title = 'Dashboard Guru'; @endphp

@if(!$teacher)
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill"></i>
        Profil guru belum dikonfigurasi. Hubungi admin untuk setup data guru Anda.
    </div>
@else

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $todaySchedules->count() }}</div>
            <div class="stat-label">Jadwal Hari Ini</div>
        </div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $recentGrades->count() }}</div>
            <div class="stat-label">Nilai Terbaru</div>
        </div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="bi bi-folder-check"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ ($documents['approved'] ?? 0) }}/{{ array_sum($documents) ?: 0 }}</div>
            <div class="stat-label">Dokumen Disetujui</div>
        </div>
    </div>
    @if($homeroomClass)
    <div class="stat-card cyan">
        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $homeroomStudents }}</div>
            <div class="stat-label">Siswa {{ $homeroomClass->name }}</div>
        </div>
    </div>
    @endif
</div>

<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Today's Schedule -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="bi bi-clock-fill" style="color: var(--accent-light);"></i> Jadwal Hari Ini</div>
            <a href="{{ route('attendance.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-clipboard-check"></i> Input Absensi
            </a>
        </div>
        @if($todaySchedules->isEmpty())
            <div class="empty-state" style="padding: 24px;">
                <i class="bi bi-calendar-x"></i>
                <p>Tidak ada jadwal hari ini</p>
            </div>
        @else
            @foreach($todaySchedules as $schedule)
            <div style="display: flex; align-items: center; gap: 16px; padding: 14px; border-radius: 12px; background: rgba(30, 41, 59, 0.5); margin-bottom: 8px; border-left: 3px solid var(--primary-light);">
                <div style="min-width: 70px; text-align: center;">
                    <div style="font-size: 14px; font-weight: 700; color: var(--primary-light);">Jam Ke-</div>
                    <div style="font-size: 16px; font-weight: 700; color: var(--text-primary);">{{ $schedule->lesson_hour }}</div>
                </div>
                <div>
                    <div style="font-weight: 600; color: var(--text-primary); font-size: 14px;">{{ $schedule->subject->name }}</div>
                    <div style="font-size: 12px; color: var(--text-muted);">Kelas {{ $schedule->classRoom->name }}</div>
                </div>
            </div>
            @endforeach
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="bi bi-lightning-fill" style="color: var(--warning-light);"></i> Aksi Cepat</div>
        </div>
        <div style="display: grid; gap: 12px;">
            <a href="{{ route('attendance.create') }}" class="btn btn-secondary" style="justify-content: start; padding: 16px 20px;">
                <i class="bi bi-clipboard-check-fill" style="font-size: 20px; color: var(--success-light);"></i>
                <div style="text-align: left;">
                    <div style="font-weight: 600; color: var(--text-primary);">Input Absensi</div>
                    <div style="font-size: 11px; color: var(--text-muted);">Catat kehadiran siswa</div>
                </div>
            </a>
            <a href="{{ route('grades.create') }}" class="btn btn-secondary" style="justify-content: start; padding: 16px 20px;">
                <i class="bi bi-journal-plus" style="font-size: 20px; color: var(--primary-light);"></i>
                <div style="text-align: left;">
                    <div style="font-weight: 600; color: var(--text-primary);">Input Nilai</div>
                    <div style="font-size: 11px; color: var(--text-muted);">Masukkan nilai siswa</div>
                </div>
            </a>
            <a href="{{ route('teaching-documents.create') }}" class="btn btn-secondary" style="justify-content: start; padding: 16px 20px;">
                <i class="bi bi-cloud-arrow-up-fill" style="font-size: 20px; color: var(--secondary-light);"></i>
                <div style="text-align: left;">
                    <div style="font-weight: 600; color: var(--text-primary);">Upload Dokumen</div>
                    <div style="font-size: 11px; color: var(--text-muted);">Upload perangkat mengajar</div>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Recent Grades -->
<div class="card">
    <div class="card-header">
        <div class="card-title">Nilai Terbaru</div>
        <a href="{{ route('grades.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
    </div>
    @if($recentGrades->isEmpty())
        <div class="empty-state" style="padding: 24px;"><p>Belum ada nilai diinput</p></div>
    @else
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Siswa</th><th>Kelas</th><th>Mapel</th><th>Jenis</th><th>Nilai</th></tr></thead>
                <tbody>
                    @foreach($recentGrades as $g)
                    <tr>
                        <td style="font-weight: 600; color: var(--text-primary);">{{ $g->student->name }}</td>
                        <td>{{ $g->classRoom->name }}</td>
                        <td>{{ $g->subject->name }}</td>
                        <td><span class="badge bg-primary">{{ $g->type_label }}</span></td>
                        <td style="font-weight: 700; color: {{ $g->score >= 75 ? 'var(--success-light)' : 'var(--danger-light)' }};">{{ $g->score }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endif
@endsection
