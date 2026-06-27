@extends('layouts.app')
@section('content')
@php $title = 'Dashboard Kepala Sekolah'; @endphp

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total_students'] }}</div>
            <div class="stat-label">Total Siswa Aktif</div>
        </div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon"><i class="bi bi-person-badge-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total_teachers'] }}</div>
            <div class="stat-label">Total Guru</div>
        </div>
    </div>
    <div class="stat-card cyan">
        <div class="stat-icon"><i class="bi bi-building"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total_classes'] }}</div>
            <div class="stat-label">Total Kelas</div>
        </div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="bi bi-folder-check"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ ($documentStats['approved'] ?? 0) }}/{{ array_sum($documentStats) ?: 0 }}</div>
            <div class="stat-label">Dokumen Disetujui</div>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Attendance per Class -->
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Kehadiran Per Kelas Hari Ini</div>
                <div class="card-subtitle">{{ now()->format('l, d F Y') }}</div>
            </div>
        </div>
        <div class="chart-container" style="height: 280px;">
            <canvas id="classAttendanceChart"></canvas>
        </div>
    </div>

    <!-- Average Grades per Subject -->
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Rata-rata Nilai Per Mapel</div>
                <div class="card-subtitle">Tahun ajaran aktif</div>
            </div>
        </div>
        <div class="chart-container" style="height: 280px;">
            <canvas id="gradeChart"></canvas>
        </div>
    </div>
</div>

<!-- Low Attendance Alert -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-exclamation-triangle-fill" style="color: var(--warning-light);"></i> Siswa dengan Kehadiran Rendah (30 hari)</div>
    </div>
    @if($lowAttendanceStudents->isEmpty())
        <div class="empty-state" style="padding: 24px;">
            <p style="color: var(--success-light);"><i class="bi bi-check-circle-fill"></i> Semua siswa memiliki kehadiran baik!</p>
        </div>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jumlah Tidak Hadir</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowAttendanceStudents as $s)
                    <tr>
                        <td style="font-weight: 600; color: var(--text-primary);">{{ $s->name }}</td>
                        <td>{{ $s->class_name }}</td>
                        <td>{{ $s->absent_count }}x</td>
                        <td>
                            @if($s->absent_count >= 5)
                                <span class="badge bg-danger">Perlu Perhatian</span>
                            @else
                                <span class="badge bg-warning">Peringatan</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Teaching Documents Status -->
<div class="card">
    <div class="card-header">
        <div class="card-title">Status Perangkat Mengajar</div>
    </div>
    <div style="display: flex; gap: 16px; flex-wrap: wrap;">
        @php
            $docTotal = array_sum($documentStats) ?: 1;
        @endphp
        <div style="flex: 1; min-width: 120px; text-align: center; padding: 16px; background: rgba(100, 116, 139, 0.1); border-radius: 12px;">
            <div style="font-size: 24px; font-weight: 800; color: var(--text-muted);">{{ $documentStats['draft'] ?? 0 }}</div>
            <div style="font-size: 12px; color: var(--text-muted);">Draft</div>
        </div>
        <div style="flex: 1; min-width: 120px; text-align: center; padding: 16px; background: rgba(6, 182, 212, 0.1); border-radius: 12px;">
            <div style="font-size: 24px; font-weight: 800; color: var(--accent-light);">{{ $documentStats['submitted'] ?? 0 }}</div>
            <div style="font-size: 12px; color: var(--accent-light);">Menunggu Review</div>
        </div>
        <div style="flex: 1; min-width: 120px; text-align: center; padding: 16px; background: rgba(5, 150, 105, 0.1); border-radius: 12px;">
            <div style="font-size: 24px; font-weight: 800; color: var(--success-light);">{{ $documentStats['approved'] ?? 0 }}</div>
            <div style="font-size: 12px; color: var(--success-light);">Disetujui</div>
        </div>
        <div style="flex: 1; min-width: 120px; text-align: center; padding: 16px; background: rgba(220, 38, 38, 0.1); border-radius: 12px;">
            <div style="font-size: 24px; font-weight: 800; color: var(--danger-light);">{{ $documentStats['rejected'] ?? 0 }}</div>
            <div style="font-size: 12px; color: var(--danger-light);">Ditolak</div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Class Attendance Chart
    const classData = @json($classAttendance);
    const classNames = Object.keys(classData);

    new Chart(document.getElementById('classAttendanceChart'), {
        type: 'bar',
        data: {
            labels: classNames,
            datasets: [
                { label: 'Hadir', data: classNames.map(c => classData[c]?.hadir || 0), backgroundColor: '#34D399', borderRadius: 6 },
                { label: 'Izin', data: classNames.map(c => classData[c]?.izin || 0), backgroundColor: '#22D3EE', borderRadius: 6 },
                { label: 'Sakit', data: classNames.map(c => classData[c]?.sakit || 0), backgroundColor: '#FBBF24', borderRadius: 6 },
                { label: 'Alpha', data: classNames.map(c => classData[c]?.alpha || 0), backgroundColor: '#F87171', borderRadius: 6 },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } } },
            scales: {
                y: { beginAtZero: true, stacked: true, grid: { color: 'rgba(51, 65, 85, 0.5)' } },
                x: { stacked: true, grid: { display: false } }
            }
        }
    });

    // Grade Radar Chart
    const gradeData = @json($subjectGrades);
    new Chart(document.getElementById('gradeChart'), {
        type: 'radar',
        data: {
            labels: gradeData.map(g => g.name),
            datasets: [{
                label: 'Rata-rata Nilai',
                data: gradeData.map(g => g.avg_score),
                borderColor: '#A78BFA',
                backgroundColor: 'rgba(167, 139, 250, 0.15)',
                borderWidth: 2,
                pointBackgroundColor: '#A78BFA',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: 'rgba(51, 65, 85, 0.5)' },
                    angleLines: { color: 'rgba(51, 65, 85, 0.5)' },
                }
            },
            plugins: { legend: { display: false } }
        }
    });
</script>
@endsection
