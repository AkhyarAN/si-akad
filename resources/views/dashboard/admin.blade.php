@extends('layouts.app')
@section('content')
@php $title = 'Dashboard Admin'; @endphp

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total_students'] }}</div>
            <div class="stat-label">Total Siswa</div>
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
        <div class="stat-icon"><i class="bi bi-book-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total_subjects'] }}</div>
            <div class="stat-label">Mata Pelajaran</div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Attendance Today -->
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Kehadiran Hari Ini</div>
                <div class="card-subtitle">{{ now()->format('l, d F Y') }}</div>
            </div>
            <span class="badge bg-primary">Live</span>
        </div>
        <div class="chart-container" style="height: 260px;">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    <!-- Attendance Trend -->
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Tren Kehadiran</div>
                <div class="card-subtitle">7 hari terakhir</div>
            </div>
        </div>
        <div class="chart-container" style="height: 260px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>
</div>

<!-- Announcements -->
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-megaphone-fill" style="color: var(--warning-light);"></i> Pengumuman Terbaru</div>
    </div>
    @if($announcements->isEmpty())
        <div class="empty-state">
            <i class="bi bi-megaphone"></i>
            <p>Belum ada pengumuman</p>
        </div>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Pembuat</th>
                        <th>Target</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($announcements as $a)
                    <tr>
                        <td style="font-weight: 600; color: var(--text-primary);">{{ $a->title }}</td>
                        <td>{{ $a->author->name }}</td>
                        <td><span class="badge bg-info">{{ ucfirst($a->target) }}</span></td>
                        <td>{{ $a->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
    // Attendance Pie Chart
    const attData = @json($todayAttendance);
    new Chart(document.getElementById('attendanceChart'), {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
            datasets: [{
                data: [attData.hadir || 0, attData.izin || 0, attData.sakit || 0, attData.alpha || 0],
                backgroundColor: ['#34D399', '#22D3EE', '#FBBF24', '#F87171'],
                borderWidth: 0,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10 }
                }
            }
        }
    });

    // Trend Line Chart
    const trendData = @json($attendanceTrend);
    const dates = Object.keys(trendData).sort();
    const hadirArr = dates.map(d => {
        const items = trendData[d] || [];
        const found = items.find(i => i.status === 'hadir');
        return found ? found.total : 0;
    });
    const absentArr = dates.map(d => {
        const items = trendData[d] || [];
        return items.filter(i => i.status !== 'hadir').reduce((sum, i) => sum + i.total, 0);
    });

    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: dates.map(d => new Date(d).toLocaleDateString('id-ID', {day: 'numeric', month: 'short'})),
            datasets: [
                {
                    label: 'Hadir',
                    data: hadirArr,
                    borderColor: '#34D399',
                    backgroundColor: 'rgba(52, 211, 153, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                },
                {
                    label: 'Tidak Hadir',
                    data: absentArr,
                    borderColor: '#F87171',
                    backgroundColor: 'rgba(248, 113, 113, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(51, 65, 85, 0.5)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Auto-refresh attendance data
    setInterval(() => {
        fetch('{{ route("api.attendance-chart") }}')
            .then(r => r.json())
            .then(data => {
                // Update chart data
            }).catch(() => {});
    }, 30000);
</script>
@endsection
