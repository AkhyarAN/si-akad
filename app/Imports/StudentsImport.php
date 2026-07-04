<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StudentsImport implements ToCollection, WithHeadingRow
{
    protected $classRoomId;

    public function __construct($classRoomId = null)
    {
        $this->classRoomId = $classRoomId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Flexible Dapodik/Standard Headers Detection
            $name = $this->getValue($row, ['nama', 'nama_lengkap', 'nama_siswa', 'nama_peserta_didik']);
            $nis = $this->getValue($row, ['nis', 'nipd', 'nomor_induk', 'nomor_induk_siswa']);
            $nisn = $this->getValue($row, ['nisn']);
            
            // Gender detection
            $genderRaw = $this->getValue($row, ['jk', 'jenis_kelamin', 'gender']);
            $gender = 'L'; // default
            if ($genderRaw && stripos($genderRaw, 'p') !== false) {
                $gender = 'P';
            }

            if (!$name || !$nis) {
                continue; // Skip if mandatory fields are missing
            }

            // Clean NIS to be used as unique identifier
            $nis = preg_replace('/[^0-9]/', '', $nis);
            if (empty($nis)) {
                $nis = rand(100000, 999999);
            }

            // Create or update User
            $user = clone User::where('username', $nis)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'username' => $nis,
                    'email' => $nis . '@student.com',
                    'password' => Hash::make($nis),
                ]);
                $user->assignRole('siswa');
            } else {
                $user->update(['name' => $name]);
            }

            // Create or update Student
            Student::updateOrCreate(
                ['nis' => $nis],
                [
                    'user_id' => $user->id,
                    'class_room_id' => $this->classRoomId,
                    'nisn' => $nisn,
                    'name' => $name,
                    'gender' => $gender,
                    'is_active' => true,
                    // Parse other dapodik fields if available
                    'birth_place' => $this->getValue($row, ['tempat_lahir']),
                    'birth_date' => $this->parseDate($this->getValue($row, ['tanggal_lahir', 'tgl_lahir'])),
                    'address' => $this->getValue($row, ['alamat', 'jalan', 'alamat_jalan']),
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

    private function parseDate($dateString)
    {
        if (!$dateString) return null;
        
        // Handle standard YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
            return $dateString;
        }

        // Handle DD/MM/YYYY or DD-MM-YYYY
        if (preg_match('/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/', $dateString, $matches)) {
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }

        return null;
    }
}
