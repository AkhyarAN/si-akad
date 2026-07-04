@extends('layouts.app')
@section('content')
@php $title = 'Rencana Penilaian'; $breadcrumb = 'Akademik / Rencana Penilaian'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Daftar Rencana Penilaian & Tugas</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Kelola jadwal ulangan harian, remidi, atau ujian.</p>
    </div>
    <a href="{{ route('exam-plans.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Buat Jadwal Baru
    </a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Jenis</th>
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th>Tanggal</th>
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($examPlans as $plan)
                <tr>
                    <td style="font-weight: 600;">
                        {{ $plan->title }}
                        <div style="font-size: 11px; color: var(--text-muted); font-weight: normal; margin-top: 4px;">{{ Str::limit($plan->description, 50) }}</div>
                    </td>
                    <td>
                        @if($plan->type == 'catatan_sikap')
                            <span class="badge bg-secondary">Catatan Sikap</span>
                        @elseif($plan->type == 'formatif')
                            <span class="badge bg-primary">Formatif</span>
                        @elseif($plan->type == 'sts')
                            <span class="badge bg-warning text-dark">STS</span>
                        @elseif($plan->type == 'sas')
                            <span class="badge bg-danger">SAS</span>
                        @elseif($plan->type == 'kokurikuler')
                            <span class="badge bg-info">Kokurikuler</span>
                        @else
                            <span class="badge bg-danger">{{ strtoupper($plan->type) }}</span>
                        @endif
                    </td>
                    <td>{{ $plan->subject->name }}</td>
                    <td>{{ $plan->classRoom->name }}</td>
                    <td>
                        <span class="badge {{ $plan->date < today() ? 'bg-secondary' : 'bg-success' }}">
                            {{ $plan->date->format('d M Y') }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('exam-plans.edit', $plan) }}" class="btn btn-sm btn-secondary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('exam-plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 32px 0;">Belum ada rencana penilaian yang dibuat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top: 20px;">
        {{ $examPlans->links() }}
    </div>
</div>
@endsection
