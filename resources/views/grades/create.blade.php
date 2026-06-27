@extends('layouts.app')
@section('content')
@php $title = 'Input Nilai'; $breadcrumb = 'Penilaian / Input'; @endphp

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-journal-plus" style="color: var(--primary-light);"></i> Input Nilai Siswa</div>
    </div>

    <form id="gradeForm" method="POST" action="{{ route('grades.store') }}">
        @csrf

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
            <div class="form-group">
                <label class="form-label">Kelas *</label>
                <select name="class_room_id" id="classSelect" class="form-control" required onchange="loadGradeStudents()">
                    <option value="">Pilih Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Mata Pelajaran *</label>
                <select name="subject_id" class="form-control" required>
                    <option value="">Pilih Mapel</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} (KKM: {{ $s->kkm }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Jenis Penilaian *</label>
                <select name="type" class="form-control" required>
                    <option value="">Pilih Jenis</option>
                    <option value="tugas">Tugas</option>
                    <option value="ulangan_harian">Ulangan Harian</option>
                    <option value="uts">UTS</option>
                    <option value="uas">UAS</option>
                    <option value="praktik">Praktik</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <input type="text" name="description" class="form-control" placeholder="Contoh: Tugas Bab 1">
            </div>
        </div>

        <div id="gradeStudentsList" style="display: none;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th style="width: 150px;">Nilai (0-100)</th>
                        </tr>
                    </thead>
                    <tbody id="gradeStudentsBody"></tbody>
                </table>
            </div>

            <div style="margin-top: 24px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Nilai</button>
                <a href="{{ route('grades.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
function loadGradeStudents() {
    const classId = document.getElementById('classSelect').value;
    if (!classId) return;

    fetch(`{{ route('attendance.students') }}?class_room_id=${classId}`)
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('gradeStudentsBody');
            tbody.innerHTML = '';

            data.students.forEach((student, i) => {
                tbody.innerHTML += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${student.nis}</td>
                        <td style="font-weight: 600; color: var(--text-primary);">${student.name}</td>
                        <td>
                            <input type="hidden" name="grades[${i}][student_id]" value="${student.id}">
                            <input type="number" name="grades[${i}][score]" class="form-control" min="0" max="100" step="0.1"
                                   placeholder="0-100" required style="padding: 8px 12px; text-align: center; font-weight: 700;">
                        </td>
                    </tr>
                `;
            });

            document.getElementById('gradeStudentsList').style.display = 'block';
        });
}
</script>
@endsection
