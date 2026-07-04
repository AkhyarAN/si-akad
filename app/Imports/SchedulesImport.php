<?php

namespace App\Imports;

use App\Models\Schedule;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\AcademicYear;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class SchedulesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $activeYear = AcademicYear::getActive();
        if (!$activeYear) return;

        foreach ($rows as $row) {
            $hari = $this->getValue($row, ['hari', 'day']);
            $jamKe = $this->getValue($row, ['jam_ke', 'lesson_hour', 'jam']);
            $namaKelas = $this->getValue($row, ['nama_kelas', 'kelas', 'class']);
            $kodeMapel = $this->getValue($row, ['kode_mapel', 'mapel', 'subject']);
            $nipGuru = $this->getValue($row, ['nip_guru', 'guru', 'nip', 'teacher']);
            
            if (!$hari || !$jamKe || !$namaKelas || !$kodeMapel || !$nipGuru) {
                continue; // Skip incomplete rows
            }

            // Find related models
            $classRoom = ClassRoom::where('academic_year_id', $activeYear->id)
                ->where('name', $namaKelas)
                ->first();
                
            $subject = Subject::where('code', $kodeMapel)
                ->orWhere('name', $kodeMapel)
                ->first();
                
            $teacher = Teacher::where('nip', $nipGuru)
                ->orWhere('name', $nipGuru)
                ->first();

            if ($classRoom && $subject && $teacher) {
                Schedule::updateOrCreate(
                    [
                        'academic_year_id' => $activeYear->id,
                        'class_room_id' => $classRoom->id,
                        'day' => ucfirst(strtolower($hari)),
                        'lesson_hour' => $jamKe,
                    ],
                    [
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                    ]
                );
            }
        }
    }

    private function getValue($row, array $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            if (isset($row[$key])) {
                return trim($row[$key]);
            }
        }
        return null;
    }
}
