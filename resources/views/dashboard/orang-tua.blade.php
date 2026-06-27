@extends('layouts.app')
@section('content')
@php $title = 'Dashboard Orang Tua'; @endphp

@if(!$parentProfile)
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill"></i>
        Profil orang tua belum dikonfigurasi. Hubungi admin sekolah.
    </div>
@else
    @foreach($studentsData as $data)
    @php $student = $data['student']; @endphp

    <div class="card" style="margin-bottom: 24px; border-left: 4px solid var(--primary-light);">
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 24px;">
            <div class="user-avatar" style="width: 64px; height: 64px; font-size: 22px; border-radius: 16px;">
                {{ strtoupper(substr($student->name, 0, 2)) }}
            </div>
            <div>
                <h3 style="font-size: 20px; font-weight: 700;">{{ $student->name }}</h3>
                <p style="color: var(--text-muted); font-size: 14px;">
                    NIS: {{ $student->nis }} &bull; Kelas {{ $student->classRoom?->name ?? '-' }}
                </p>
            </div>
        </div>

        <!-- Attendance & Grade Stats -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 12px; margin-bottom: 24px;">
            <div style="text-align: center; padding: 16px; background: rgba(52, 211, 153, 0.1); border-radius: 12px;">
                <div style="font-size: 28px; font-weight: 800; color: var(--success-light);">{{ $data['attendance']['hadir'] ?? 0 }}</div>
                <div style="font-size: 11px; color: var(--text-muted);">Hadir</div>
            </div>
            <div style="text-align: center; padding: 16px; background: rgba(34, 211, 238, 0.1); border-radius: 12px;">
                <div style="font-size: 28px; font-weight: 800; color: var(--accent-light);">{{ $data['attendance']['izin'] ?? 0 }}</div>
                <div style="font-size: 11px; color: var(--text-muted);">Izin</div>
            </div>
            <div style="text-align: center; padding: 16px; background: rgba(251, 191, 36, 0.1); border-radius: 12px;">
                <div style="font-size: 28px; font-weight: 800; color: var(--warning-light);">{{ $data['attendance']['sakit'] ?? 0 }}</div>
                <div style="font-size: 11px; color: var(--text-muted);">Sakit</div>
            </div>
            <div style="text-align: center; padding: 16px; background: rgba(248, 113, 113, 0.1); border-radius: 12px;">
                <div style="font-size: 28px; font-weight: 800; color: var(--danger-light);">{{ $data['attendance']['alpha'] ?? 0 }}</div>
                <div style="font-size: 11px; color: var(--text-muted);">Alpha</div>
            </div>
        </div>

        <div class="grid-2">
            <!-- Grade Chart -->
            <div>
                <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 16px;">
                    <i class="bi bi-graph-up" style="color: var(--secondary-light);"></i> Rata-rata Nilai Per Mapel
                </h4>
                <div class="chart-container" style="height: 220px;">
                    <canvas id="gradeChart{{ $loop->index }}"></canvas>
                </div>
            </div>

            <!-- Recent Grades -->
            <div>
                <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 16px;">
                    <i class="bi bi-journal-bookmark-fill" style="color: var(--primary-light);"></i> Nilai Terbaru
                </h4>
                @if($data['recentGrades']->isEmpty())
                    <p style="color: var(--text-muted); font-size: 13px;">Belum ada nilai.</p>
                @else
                    @foreach($data['recentGrades'] as $g)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: rgba(30, 41, 59, 0.5); border-radius: 10px; margin-bottom: 6px;">
                        <div>
                            <div style="font-weight: 600; font-size: 13px; color: var(--text-primary);">{{ $g->subject->name }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">{{ $g->type_label }}</div>
                        </div>
                        <div style="font-size: 18px; font-weight: 800; color: {{ $g->score >= 75 ? 'var(--success-light)' : 'var(--danger-light)' }};">
                            {{ $g->score }}
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    @endforeach
@endif

@endsection

@section('scripts')
<script>
@foreach($studentsData as $data)
    @if($data['subjectAverages']->isNotEmpty())
    @php
        $bgColors = $data['subjectAverages']->pluck('avg_score')->map(function($s) {
            return $s >= 75 ? 'rgba(52, 211, 153, 0.6)' : 'rgba(248, 113, 113, 0.6)';
        })->values();
    @endphp
    new Chart(document.getElementById('gradeChart{{ $loop->index }}'), {
        type: 'bar',
        data: {
            labels: @json($data['subjectAverages']->pluck('name')),
            datasets: [{
                label: 'Rata-rata',
                data: @json($data['subjectAverages']->pluck('avg_score')),
                backgroundColor: @json($bgColors),
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, max: 100, grid: { color: 'rgba(51, 65, 85, 0.5)' } },
                x: { grid: { display: false } }
            }
        }
    });
    @endif
@endforeach
</script>
@endsection
