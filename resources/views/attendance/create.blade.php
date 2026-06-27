@extends('layouts.app')
@section('content')
@php $title = 'Input Absensi'; $breadcrumb = 'Absensi / Input'; @endphp

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-clipboard-check-fill" style="color: var(--success-light);"></i> Input Absensi Siswa</div>
    </div>

    @if($schedules->isNotEmpty())
    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">Jadwal Anda hari ini — klik untuk mengisi absensi:</p>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; margin-bottom: 24px;">
        @foreach($schedules as $schedule)
        <button type="button" class="btn btn-secondary schedule-btn" style="justify-content: start; padding: 16px;"
            data-class="{{ $schedule->class_room_id }}" data-subject="{{ $schedule->subject_id }}"
            onclick="selectSchedule(this, {{ $schedule->class_room_id }}, {{ $schedule->subject_id }})">
            <div style="text-align: left;">
                <div style="font-weight: 700; color: var(--text-primary);">{{ $schedule->subject->name }}</div>
                <div style="font-size: 12px; color: var(--text-muted);">Kelas {{ $schedule->classRoom->name }} &bull; {{ $schedule->time_range }}</div>
            </div>
        </button>
        @endforeach
    </div>
    <hr style="border-color: var(--border-color); margin-bottom: 20px;">
    @endif

    <form id="attendanceForm" method="POST" action="{{ route('attendance.store') }}">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div class="form-group">
                <label class="form-label">Kelas *</label>
                <select name="class_room_id" id="classSelect" class="form-control" required onchange="loadStudents()">
                    <option value="">Pilih Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Mata Pelajaran *</label>
                <select name="subject_id" id="subjectSelect" class="form-control" required>
                    <option value="">Pilih Mapel</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal *</label>
                <input type="date" name="date" id="dateInput" class="form-control" value="{{ today()->format('Y-m-d') }}" required>
            </div>
        </div>

        <!-- Students List -->
        <div id="studentsList" style="display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h4 style="font-size: 15px; font-weight: 700;">Daftar Siswa</h4>
                <div style="display: flex; gap: 8px;">
                    <button type="button" class="btn btn-sm btn-success" onclick="setAllStatus('hadir')">
                        <i class="bi bi-check-all"></i> Semua Hadir
                    </button>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th style="width: 260px;">Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="studentsBody"></tbody>
                </table>
            </div>

            <div style="margin-top: 24px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Absensi
                </button>
                <a href="{{ route('attendance.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>

        <div id="loadingState" style="display: none; text-align: center; padding: 40px; color: var(--text-muted);">
            <div style="animation: pulse 1.5s infinite;">Memuat data siswa...</div>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
function selectSchedule(btn, classId, subjectId) {
    document.querySelectorAll('.schedule-btn').forEach(b => b.style.borderColor = 'var(--border-color)');
    btn.style.borderColor = 'var(--primary-light)';

    document.getElementById('classSelect').value = classId;
    document.getElementById('subjectSelect').value = subjectId;
    loadStudents();
}

function loadStudents() {
    const classId = document.getElementById('classSelect').value;
    const subjectId = document.getElementById('subjectSelect').value;
    const date = document.getElementById('dateInput').value;

    if (!classId) return;

    document.getElementById('studentsList').style.display = 'none';
    document.getElementById('loadingState').style.display = 'block';

    fetch(`{{ route('attendance.students') }}?class_room_id=${classId}&subject_id=${subjectId}&date=${date}`)
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('studentsBody');
            tbody.innerHTML = '';

            data.students.forEach((student, i) => {
                const existing = data.existing[student.id] || 'hadir';
                tbody.innerHTML += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${student.nis}</td>
                        <td style="font-weight: 600; color: var(--text-primary);">${student.name}</td>
                        <td>
                            <input type="hidden" name="attendance[${i}][student_id]" value="${student.id}">
                            <div style="display: flex; gap: 4px;">
                                ${['hadir','izin','sakit','alpha'].map(s => `
                                    <label style="flex: 1; text-align: center; padding: 8px 4px; border-radius: 8px; cursor: pointer;
                                        background: ${existing === s ? statusColor(s) : 'var(--bg-card)'}; border: 1px solid var(--border-color);
                                        font-size: 11px; font-weight: 600; color: ${existing === s ? '#fff' : 'var(--text-muted)'}; transition: all 0.2s;">
                                        <input type="radio" name="attendance[${i}][status]" value="${s}" ${existing === s ? 'checked' : ''} style="display: none;"
                                            onchange="this.closest('div').querySelectorAll('label').forEach(l => { l.style.background = 'var(--bg-card)'; l.style.color = 'var(--text-muted)'; }); this.parentElement.style.background = statusColor('${s}'); this.parentElement.style.color = '#fff';">
                                        ${s.charAt(0).toUpperCase() + s.slice(1)}
                                    </label>
                                `).join('')}
                            </div>
                        </td>
                        <td>
                            <input type="text" name="attendance[${i}][notes]" class="form-control" placeholder="Catatan" style="padding: 8px 12px; font-size: 12px;">
                        </td>
                    </tr>
                `;
            });

            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('studentsList').style.display = 'block';
        });
}

function statusColor(status) {
    const colors = { hadir: '#059669', izin: '#0891B2', sakit: '#D97706', alpha: '#DC2626' };
    return colors[status] || '#64748B';
}

function setAllStatus(status) {
    document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(r => {
        r.checked = true;
        r.dispatchEvent(new Event('change'));
    });
}
</script>
@endsection
