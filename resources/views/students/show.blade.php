@extends('layouts.app')
@section('content')
@php $title = 'Detail Siswa'; $breadcrumb = 'Siswa / Detail'; @endphp

<div class="card" style="margin-bottom: 24px;">
    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 24px;">
        <div class="user-avatar" style="width: 72px; height: 72px; font-size: 24px; border-radius: 18px;">
            {{ strtoupper(substr($student->name, 0, 2)) }}
        </div>
        <div>
            <h3 style="font-size: 22px; font-weight: 700;">{{ $student->name }}</h3>
            <p style="color: var(--text-muted);">NIS: {{ $student->nis }} &bull; NISN: {{ $student->nisn ?? '-' }} &bull; Kelas {{ $student->classRoom?->name ?? '-' }}</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
        <div>
            <p style="color: var(--text-muted); font-size: 12px;">Jenis Kelamin</p>
            <p style="font-weight: 600;">{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
        </div>
        <div>
            <p style="color: var(--text-muted); font-size: 12px;">TTL</p>
            <p style="font-weight: 600;">{{ $student->birth_place }}, {{ $student->birth_date?->format('d/m/Y') ?? '-' }}</p>
        </div>
        <div>
            <p style="color: var(--text-muted); font-size: 12px;">Agama</p>
            <p style="font-weight: 600;">{{ $student->religion ?? '-' }}</p>
        </div>
        <div>
            <p style="color: var(--text-muted); font-size: 12px;">Orang Tua</p>
            <p style="font-weight: 600;">{{ $student->parent?->name ?? '-' }}</p>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Recent Attendance -->
    <div class="card">
        <div class="card-header"><div class="card-title">Kehadiran Terbaru</div></div>
        @php $recentAtt = $student->attendances()->latest()->take(10)->get(); @endphp
        @if($recentAtt->isEmpty())
            <p style="color: var(--text-muted); font-size: 13px;">Belum ada data absensi.</p>
        @else
            @foreach($recentAtt as $att)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                <div>
                    <span style="font-size: 13px;">{{ $att->date->format('d/m/Y') }}</span>
                    <span style="color: var(--text-muted); font-size: 12px;"> - {{ $att->subject?->name }}</span>
                </div>
                {!! $att->status_badge !!}
            </div>
            @endforeach
        @endif
    </div>

    <!-- Recent Grades -->
    <div class="card">
        <div class="card-header"><div class="card-title">Nilai Terbaru</div></div>
        @php $recentGrades = $student->grades()->with('subject')->latest()->take(10)->get(); @endphp
        @if($recentGrades->isEmpty())
            <p style="color: var(--text-muted); font-size: 13px;">Belum ada data nilai.</p>
        @else
            @foreach($recentGrades as $g)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                <div>
                    <span style="font-size: 13px; font-weight: 600;">{{ $g->subject->name }}</span>
                    <span style="color: var(--text-muted); font-size: 12px;"> - {{ $g->type_label }}</span>
                </div>
                <span style="font-weight: 700; color: {{ $g->score >= 75 ? 'var(--success-light)' : 'var(--danger-light)' }};">{{ $g->score }}</span>
            </div>
            @endforeach
        @endif
    </div>
</div>

<div style="margin-top: 24px;">
    <a href="{{ route('students.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>
@endsection
