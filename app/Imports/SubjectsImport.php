<?php

namespace App\Imports;

use App\Models\Subject;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubjectsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $code = $this->getValue($row, ['kode', 'code', 'kode_mapel']);
            $name = $this->getValue($row, ['nama', 'name', 'nama_mapel']);
            
            if (!$code || !$name) {
                continue; // Skip if mandatory fields are missing
            }

            $gradeLevel = strtolower($this->getValue($row, ['tingkat', 'kelas', 'grade', 'grade_level']));
            if (!in_array($gradeLevel, ['7', '8', '9', 'all'])) {
                $gradeLevel = 'all'; // Default
            }

            $kkm = $this->getValue($row, ['kkm', 'kriteria_ketuntasan_minimal']);
            $kkm = is_numeric($kkm) ? intval($kkm) : 75;

            $hours = $this->getValue($row, ['jam_per_minggu', 'jam', 'hours', 'hours_per_week']);
            $hours = is_numeric($hours) ? intval($hours) : 2;

            Subject::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'grade_level' => $gradeLevel,
                    'kkm' => $kkm,
                    'hours_per_week' => $hours,
                    'is_active' => true,
                ]
            );
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
