@extends('layouts.app')
@section('content')
@php $title = 'Jadwal Pelajaran'; $breadcrumb = 'Master Data / Jadwal'; @endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Jadwal Pelajaran</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Kelola jadwal pelajaran per kelas</p>
    </div>
    @if(isset($selectedClass))
    <button type="button" class="btn btn-primary" onclick="document.getElementById('modalAdd').classList.add('show')">
        <i class="bi bi-plus-lg"></i> Tambah Jadwal Kelas {{ $selectedClass->name }}
    </button>
    @endif
</div>

<div class="card" style="margin-bottom: 20px;">
    <form method="GET" class="filter-bar">
        <div class="form-group">
            <label class="form-label">Pilih Kelas</label>
            <select name="class_room_id" class="form-control" onchange="this.form.submit()" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_room_id') == $c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        @if(isset($selectedClass))
        <div style="margin-left: auto; display: flex; align-items: center;">
            <span class="badge bg-primary" style="font-size: 14px; padding: 8px 16px;">
                Tahun Ajaran: {{ \App\Models\AcademicYear::getActive()?->full_name }}
            </span>
        </div>
        @endif
    </form>
</div>

@if(isset($selectedClass))
<div class="card">
    @php
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
    @endphp
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        @foreach($days as $day)
            @php
                $daySchedules = $schedules->where('day', $day)->sortBy('start_time');
            @endphp
            
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden;">
                <div style="background: rgba(30, 41, 59, 0.8); padding: 12px 16px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--accent-light); text-transform: uppercase; letter-spacing: 1px;">
                    {{ $day }}
                </div>
                <div style="padding: 16px;">
                    @if($daySchedules->isEmpty())
                        <div style="text-align: center; color: var(--text-muted); font-size: 12px; padding: 16px 0; font-style: italic;">
                            Kosong
                        </div>
                    @else
                        @foreach($daySchedules as $schedule)
                        <div style="display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px dashed rgba(51, 65, 85, 0.5);">
                            <div style="font-family: monospace; font-size: 11px; color: var(--text-muted); min-width: 80px;">
                                {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 600; color: var(--text-primary); font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $schedule->subject->name }}">
                                    {{ $schedule->subject->name }}
                                </div>
                                <div style="font-size: 11px; color: var(--secondary-light); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $schedule->teacher->name }}">
                                    {{ $schedule->teacher->name }}
                                </div>
                            </div>
                            <form method="POST" action="{{ route('master.schedules.delete', $schedule) }}" onsubmit="return confirm('Hapus jadwal ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: var(--danger-light); cursor: pointer; padding: 4px; opacity: 0.6; transition: opacity 0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.6">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal Add -->
<div class="modal-backdrop" id="modalAdd">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 class="modal-title" style="margin: 0;">Tambah Jadwal: Kelas {{ $selectedClass->name }}</h3>
            <button type="button" onclick="document.getElementById('modalAdd').classList.remove('show')" style="background: none; border: none; color: var(--text-muted); font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        
        <form method="POST" action="{{ route('master.schedules.store') }}">
            @csrf
            <input type="hidden" name="class_room_id" value="{{ $selectedClass->id }}">
            
            <div class="form-group">
                <label class="form-label">Hari *</label>
                <select name="day" class="form-control" required>
                    <option value="senin">Senin</option>
                    <option value="selasa">Selasa</option>
                    <option value="rabu">Rabu</option>
                    <option value="kamis">Kamis</option>
                    <option value="jumat">Jumat</option>
                    <option value="sabtu">Sabtu</option>
                </select>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Jam Mulai *</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jam Selesai *</label>
                    <input type="time" name="end_time" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Mata Pelajaran *</label>
                <select name="subject_id" class="form-control" required>
                    <option value="">Pilih Mapel</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Guru Pengajar *</label>
                <select name="teacher_id" class="form-control" required>
                    <option value="">Pilih Guru</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} {{ $t->specialization ? '('.$t->specialization.')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            
            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalAdd').classList.remove('show')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@else
<div class="empty-state card">
    <i class="bi bi-calendar-week"></i>
    <p>Silakan pilih kelas terlebih dahulu untuk melihat dan mengelola jadwal pelajaran.</p>
</div>
@endif

@endsection
