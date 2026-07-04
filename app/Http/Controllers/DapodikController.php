<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ParentModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DapodikController extends Controller
{
    public function index()
    {
        $settings = [
            'dapodik_url' => Setting::get('dapodik_url', ''),
            'dapodik_npsn' => Setting::get('dapodik_npsn', ''),
            'dapodik_token' => Setting::get('dapodik_token', ''),
        ];

        return view('master.dapodik', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'dapodik_url' => 'required|url',
            'dapodik_npsn' => 'required|string',
            'dapodik_token' => 'required|string',
        ]);

        Setting::set('dapodik_url', rtrim($request->dapodik_url, '/'));
        Setting::set('dapodik_npsn', $request->dapodik_npsn);
        Setting::set('dapodik_token', $request->dapodik_token);

        return back()->with('success', 'Pengaturan koneksi Dapodik berhasil disimpan!');
    }

    private function fetchDapodikApi($endpoint)
    {
        $url = Setting::get('dapodik_url');
        $npsn = Setting::get('dapodik_npsn');
        $token = Setting::get('dapodik_token');

        if (!$url || !$token) {
            throw new \Exception('URL atau Token Dapodik belum diatur.');
        }

        // Standard Dapodik Web Service typically uses Bearer token,
        // and sometimes requires npsn parameter in the URL.
        $fullUrl = $url . '/WebService/' . $endpoint . '?npsn=' . $npsn;
        
        $response = Http::withToken($token)->timeout(30)->get($fullUrl);

        if ($response->failed()) {
            throw new \Exception('Gagal terhubung ke Dapodik: ' . $response->status());
        }

        $data = $response->json();
        if (isset($data['rows'])) {
            return $data['rows']; // Typical dapodik format
        }

        return $data;
    }

    public function testConnection()
    {
        try {
            $data = $this->fetchDapodikApi('getSekolah');
            return back()->with('success', 'Koneksi Berhasil! Terhubung dengan server Dapodik.');
        } catch (\Exception $e) {
            // We return a mock success just in case it fails in a real test but user wants to proceed,
            // or we return the actual error.
            return back()->with('error', 'Koneksi Gagal: ' . $e->getMessage() . '. Pastikan IP Dapodik dan Token benar, serta server Dapodik sedang aktif.');
        }
    }

    public function syncPTK()
    {
        try {
            $ptkList = $this->fetchDapodikApi('getPtk');
            $syncedCount = 0;

            foreach ($ptkList as $ptk) {
                // Typical fields in Dapodik JSON: nama, nip, jenis_kelamin, email, no_hp
                if (empty($ptk['nama'])) continue;

                $email = !empty($ptk['email']) ? $ptk['email'] : strtolower(str_replace(' ', '', $ptk['nama'])) . rand(10,99) . '@dapodik.local';
                $nip = !empty($ptk['nip']) ? $ptk['nip'] : null;
                $gender = (isset($ptk['jenis_kelamin']) && $ptk['jenis_kelamin'] == 'L') ? 'L' : 'P';
                
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $ptk['nama'],
                        'password' => Hash::make('password123'),
                    ]
                );
                
                if (!$user->hasRole('guru')) {
                    $user->assignRole('guru');
                }

                Teacher::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip' => $nip,
                        'name' => $ptk['nama'],
                        'gender' => $gender,
                        'phone' => $ptk['no_hp'] ?? null,
                    ]
                );

                $syncedCount++;
            }

            return back()->with('success', "Berhasil menarik $syncedCount data Guru (PTK) dari Dapodik.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Sinkronisasi PTK: ' . $e->getMessage());
        }
    }

    public function syncPesertaDidik()
    {
        try {
            $pdList = $this->fetchDapodikApi('getPesertaDidik');
            $syncedCount = 0;

            foreach ($pdList as $pd) {
                if (empty($pd['nama']) || empty($pd['nisn'])) continue;

                $nisn = $pd['nisn'];
                $gender = (isset($pd['jenis_kelamin']) && $pd['jenis_kelamin'] == 'L') ? 'L' : 'P';

                // Try to find or create student
                Student::updateOrCreate(
                    ['nisn' => $nisn],
                    [
                        'nis' => $pd['nipd'] ?? $nisn,
                        'name' => $pd['nama'],
                        'gender' => $gender,
                        'birth_place' => $pd['tempat_lahir'] ?? null,
                        'birth_date' => $pd['tanggal_lahir'] ?? null,
                        'religion' => $pd['agama_id_str'] ?? null,
                        'address' => $pd['alamat_jalan'] ?? null,
                        'phone' => $pd['nomor_telepon_seluler'] ?? null,
                    ]
                );
                
                $syncedCount++;
            }

            return back()->with('success', "Berhasil menarik $syncedCount data Siswa dari Dapodik.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Sinkronisasi Peserta Didik: ' . $e->getMessage());
        }
    }

    public function syncRombonganBelajar()
    {
        try {
            $rombels = $this->fetchDapodikApi('getRombonganBelajar');
            $syncedCount = 0;
            $activeYear = \App\Models\AcademicYear::getActive();

            if (!$activeYear) {
                return back()->with('error', 'Gagal Sinkronisasi: Tidak ada Tahun Ajaran aktif.');
            }

            foreach ($rombels as $rombel) {
                if (empty($rombel['nama'])) continue;

                $grade = isset($rombel['tingkat_pendidikan_id']) ? $rombel['tingkat_pendidikan_id'] : '7';
                if ($grade > 9) $grade = 9;

                $homeroomTeacherId = null;
                // Attempt to match homeroom teacher if provided by Dapodik
                $waliKelasName = $rombel['wali_kelas'] ?? $rombel['nama_wali_kelas'] ?? $rombel['ptk_nama'] ?? null;
                if ($waliKelasName) {
                    $teacher = \App\Models\Teacher::where('name', 'like', "%{$waliKelasName}%")->first();
                    if ($teacher) {
                        $homeroomTeacherId = $teacher->id;
                    }
                }

                \App\Models\ClassRoom::updateOrCreate(
                    [
                        'name' => $rombel['nama'],
                        'academic_year_id' => $activeYear->id,
                    ],
                    [
                        'grade_level' => $grade,
                        'homeroom_teacher_id' => $homeroomTeacherId,
                    ]
                );
                
                $syncedCount++;
            }

            return back()->with('success', "Berhasil menarik $syncedCount data Rombongan Belajar (Kelas) dari Dapodik.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Sinkronisasi Rombongan Belajar: ' . $e->getMessage());
        }
    }
}
