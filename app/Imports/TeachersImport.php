<?php

namespace App\Imports;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TeachersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Flexible Dapodik/Standard Headers Detection
            $name = $this->getValue($row, ['nama', 'nama_lengkap', 'nama_guru', 'nama_ptk']);
            $nip = $this->getValue($row, ['nip', 'nuptk', 'nik']);
            $nuptk = $this->getValue($row, ['nuptk']);
            
            // Gender detection
            $genderRaw = $this->getValue($row, ['jk', 'jenis_kelamin', 'gender']);
            $gender = 'L'; // default
            if ($genderRaw && stripos($genderRaw, 'p') !== false) {
                $gender = 'P';
            }

            if (!$name || !$nip) {
                continue; // Skip if mandatory fields are missing
            }

            // Clean NIP
            $nip = preg_replace('/[^0-9]/', '', $nip);
            if (empty($nip)) {
                $nip = rand(10000000, 99999999);
            }

            $email = $this->getValue($row, ['email', 'surel']);
            if (!$email) {
                $email = $nip . '@teacher.com';
            }

            $phone = $this->getValue($row, ['phone', 'no_hp', 'hp', 'telepon']);
            $specialization = $this->getValue($row, ['specialization', 'spesialisasi', 'mapel']);

            // Create or update User
            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password123'),
                ]);
                $user->assignRole('guru');
            } else {
                $user->update([
                    'name' => $name,
                    'email' => $email,
                ]);
            }

            // Create or update Teacher
            Teacher::updateOrCreate(
                ['nip' => $nip],
                [
                    'user_id' => $user->id,
                    'nuptk' => $nuptk,
                    'name' => $name,
                    'gender' => $gender,
                    'phone' => $phone,
                    'specialization' => $specialization,
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
        
        // Handle YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
            return $dateString;
        }

        // Handle DD/MM/YYYY
        if (preg_match('/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/', $dateString, $matches)) {
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }

        return null;
    }
}
