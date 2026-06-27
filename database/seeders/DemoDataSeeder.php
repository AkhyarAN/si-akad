<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\ParentModel;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // === Create Admin User ===
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@siakad.com',
            'password' => Hash::make('admin123'),
        ]);
        $admin->assignRole('admin');

        // === Create Kepala Sekolah ===
        $kepsek = User::create([
            'name' => 'Dr. Budi Santoso, M.Pd',
            'email' => 'kepsek@siakad.com',
            'password' => Hash::make('kepsek123'),
            'phone' => '081234567890',
        ]);
        $kepsek->assignRole('kepala_sekolah');

        // === Academic Year ===
        $academicYear = AcademicYear::create([
            'year' => '2024/2025',
            'semester' => 'ganjil',
            'is_active' => true,
            'start_date' => '2024-07-15',
            'end_date' => '2024-12-20',
        ]);

        // === Subjects ===
        $subjects = [
            ['name' => 'Matematika', 'code' => 'MTK', 'kkm' => 75, 'hours_per_week' => 5],
            ['name' => 'Bahasa Indonesia', 'code' => 'BIN', 'kkm' => 75, 'hours_per_week' => 4],
            ['name' => 'Bahasa Inggris', 'code' => 'BIG', 'kkm' => 70, 'hours_per_week' => 4],
            ['name' => 'IPA', 'code' => 'IPA', 'kkm' => 70, 'hours_per_week' => 5],
            ['name' => 'IPS', 'code' => 'IPS', 'kkm' => 70, 'hours_per_week' => 4],
            ['name' => 'PKN', 'code' => 'PKN', 'kkm' => 75, 'hours_per_week' => 3],
            ['name' => 'Pendidikan Agama', 'code' => 'PAI', 'kkm' => 75, 'hours_per_week' => 3],
            ['name' => 'Seni Budaya', 'code' => 'SBD', 'kkm' => 70, 'hours_per_week' => 2],
            ['name' => 'PJOK', 'code' => 'PJK', 'kkm' => 70, 'hours_per_week' => 3],
            ['name' => 'Informatika', 'code' => 'INF', 'kkm' => 70, 'hours_per_week' => 2],
        ];

        $subjectModels = [];
        foreach ($subjects as $s) {
            $subjectModels[] = Subject::create(array_merge($s, ['grade_level' => 'all']));
        }

        // === Teachers ===
        $teacherData = [
            ['name' => 'Siti Rahmawati, S.Pd', 'nip' => '198501152010012001', 'gender' => 'P', 'specialization' => 'Matematika', 'email' => 'siti@siakad.com'],
            ['name' => 'Ahmad Fauzi, S.Pd', 'nip' => '198703202011011002', 'gender' => 'L', 'specialization' => 'Bahasa Indonesia', 'email' => 'ahmad@siakad.com'],
            ['name' => 'Dewi Lestari, S.Pd', 'nip' => '199001102012012003', 'gender' => 'P', 'specialization' => 'Bahasa Inggris', 'email' => 'dewi@siakad.com'],
            ['name' => 'Hendra Wijaya, S.Pd', 'nip' => '198812052013011004', 'gender' => 'L', 'specialization' => 'IPA', 'email' => 'hendra@siakad.com'],
            ['name' => 'Rina Susanti, S.Pd', 'nip' => '199205152014012005', 'gender' => 'P', 'specialization' => 'IPS', 'email' => 'rina@siakad.com'],
            ['name' => 'Bambang Prakoso, S.Pd', 'nip' => '198605252010011006', 'gender' => 'L', 'specialization' => 'PKN', 'email' => 'bambang@siakad.com'],
        ];

        $teachers = [];
        foreach ($teacherData as $td) {
            $user = User::create([
                'name' => $td['name'],
                'email' => $td['email'],
                'password' => Hash::make('guru123'),
                'phone' => '08' . rand(1000000000, 9999999999),
            ]);
            $user->assignRole('guru');

            $teachers[] = Teacher::create([
                'user_id' => $user->id,
                'nip' => $td['nip'],
                'name' => $td['name'],
                'gender' => $td['gender'],
                'phone' => $user->phone,
                'specialization' => $td['specialization'],
            ]);
        }

        // === Classes ===
        $classNames = ['VII-A', 'VII-B', 'VIII-A', 'VIII-B', 'IX-A', 'IX-B'];
        $gradeLevels = ['7', '7', '8', '8', '9', '9'];
        $classModels = [];

        for ($i = 0; $i < count($classNames); $i++) {
            $class = ClassRoom::create([
                'name' => $classNames[$i],
                'grade_level' => $gradeLevels[$i],
                'academic_year_id' => $academicYear->id,
                'homeroom_teacher_id' => $teachers[$i]->id,
                'capacity' => 32,
            ]);
            $classModels[] = $class;

            // Assign wali_kelas role
            $teachers[$i]->user->assignRole('wali_kelas');
        }

        // === Students & Parents ===
        $firstNames = ['Adi', 'Budi', 'Citra', 'Diana', 'Eka', 'Fani', 'Galih', 'Hana', 'Indra', 'Joko',
            'Kiki', 'Laras', 'Maya', 'Nanda', 'Omar', 'Putri', 'Rizki', 'Sari', 'Tono', 'Udin',
            'Vina', 'Wati', 'Yuli', 'Zahra', 'Arif', 'Bella', 'Dimas', 'Eva', 'Farhan', 'Gita'];

        $lastNames = ['Pratama', 'Wijaya', 'Sari', 'Putri', 'Kusuma', 'Rahayu', 'Saputra', 'Permana', 'Wibowo', 'Lestari'];

        $studentIndex = 0;
        foreach ($classModels as $class) {
            for ($j = 0; $j < 5; $j++) {
                $studentIndex++;
                $firstName = $firstNames[($studentIndex - 1) % count($firstNames)];
                $lastName = $lastNames[($studentIndex - 1) % count($lastNames)];
                $studentName = $firstName . ' ' . $lastName;
                $gender = $j % 2 === 0 ? 'L' : 'P';

                // Create parent
                $parentUser = User::create([
                    'name' => 'Orang Tua ' . $studentName,
                    'email' => 'parent' . $studentIndex . '@siakad.com',
                    'password' => Hash::make('parent123'),
                    'phone' => '08' . rand(1000000000, 9999999999),
                ]);
                $parentUser->assignRole('orang_tua');

                $parent = ParentModel::create([
                    'user_id' => $parentUser->id,
                    'name' => 'Bp/Ibu ' . $lastName,
                    'phone' => $parentUser->phone,
                    'whatsapp_number' => $parentUser->phone,
                    'relationship' => $gender === 'L' ? 'ayah' : 'ibu',
                ]);

                Student::create([
                    'nis' => '2024' . str_pad($studentIndex, 4, '0', STR_PAD_LEFT),
                    'nisn' => '00' . rand(10000000, 99999999),
                    'name' => $studentName,
                    'gender' => $gender,
                    'birth_date' => now()->subYears(rand(12, 15))->subDays(rand(0, 365)),
                    'birth_place' => 'Jakarta',
                    'religion' => 'Islam',
                    'address' => 'Jl. Contoh No. ' . $studentIndex,
                    'class_room_id' => $class->id,
                    'parent_id' => $parent->id,
                ]);
            }
        }

        // === Schedules ===
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
        $times = [
            ['07:00', '07:40'], ['07:40', '08:20'], ['08:20', '09:00'],
            ['09:15', '09:55'], ['09:55', '10:35'],
            ['10:50', '11:30'], ['11:30', '12:10'],
        ];

        foreach ($classModels as $ci => $class) {
            $subjectIndex = 0;
            foreach ($days as $day) {
                for ($t = 0; $t < min(5, count($times)); $t++) {
                    $si = $subjectIndex % count($subjectModels);
                    $ti = $si % count($teachers);
                    Schedule::create([
                        'class_room_id' => $class->id,
                        'subject_id' => $subjectModels[$si]->id,
                        'teacher_id' => $teachers[$ti]->id,
                        'academic_year_id' => $academicYear->id,
                        'day' => $day,
                        'start_time' => $times[$t][0],
                        'end_time' => $times[$t][1],
                    ]);
                    $subjectIndex++;
                }
            }
        }

        // === Sample Attendance (last 7 days) ===
        $students = Student::all();
        $statuses = ['hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alpha'];

        for ($d = 6; $d >= 0; $d--) {
            $date = now()->subDays($d);
            if ($date->isWeekend()) continue;

            foreach ($students as $student) {
                $daySchedules = Schedule::where('class_room_id', $student->class_room_id)
                    ->where('academic_year_id', $academicYear->id)
                    ->take(2)
                    ->get();

                foreach ($daySchedules as $schedule) {
                    Attendance::create([
                        'student_id' => $student->id,
                        'schedule_id' => $schedule->id,
                        'class_room_id' => $student->class_room_id,
                        'subject_id' => $schedule->subject_id,
                        'teacher_id' => $schedule->teacher_id,
                        'date' => $date->format('Y-m-d'),
                        'status' => $statuses[array_rand($statuses)],
                    ]);
                }
            }
        }

        // === Sample Grades ===
        $gradeTypes = ['tugas', 'ulangan_harian', 'uts'];
        foreach ($students as $student) {
            foreach ($subjectModels as $subject) {
                foreach ($gradeTypes as $type) {
                    Grade::create([
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'teacher_id' => $teachers[array_rand($teachers)]->id,
                        'class_room_id' => $student->class_room_id,
                        'academic_year_id' => $academicYear->id,
                        'type' => $type,
                        'description' => ucfirst(str_replace('_', ' ', $type)) . ' ' . $subject->name,
                        'score' => rand(55, 100),
                        'max_score' => 100,
                    ]);
                }
            }
        }

        // === Announcements ===
        Announcement::create([
            'title' => 'Selamat Datang di SIAKAD SMP',
            'content' => 'Sistem Informasi Akademik SMP telah aktif. Silahkan gunakan sistem ini untuk mengelola data akademik.',
            'author_id' => $admin->id,
            'target' => 'all',
            'is_published' => true,
            'published_at' => now(),
        ]);

        Announcement::create([
            'title' => 'Jadwal UTS Semester Ganjil',
            'content' => 'UTS Semester Ganjil akan dilaksanakan pada tanggal 14-18 Oktober 2024. Mohon persiapkan diri dengan baik.',
            'author_id' => $kepsek->id,
            'target' => 'all',
            'is_published' => true,
            'published_at' => now(),
        ]);
    }
}
