<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Imports\TeachersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MasterDataController extends Controller
{
    // === Academic Year ===
    public function academicYears()
    {
        $years = AcademicYear::orderByDesc('year')->orderBy('semester')->get();
        return view('master.academic-years', compact('years'));
    }

    public function storeAcademicYear(Request $request)
    {
        $request->validate([
            'year' => 'required|string',
            'semester' => 'required|in:ganjil,genap',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($request->is_active) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicYear::create($request->only(['year', 'semester', 'start_date', 'end_date', 'is_active']));
        return back()->with('success', 'Tahun ajaran berhasil ditambahkan!');
    }

    public function setActiveYear(AcademicYear $academicYear)
    {
        AcademicYear::where('is_active', true)->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);
        return back()->with('success', 'Tahun ajaran aktif berhasil diubah!');
    }

    // === Teachers ===
    public function teachers()
    {
        $teachers = Teacher::with('user')->where('is_active', true)->orderBy('name')->get();
        return view('master.teachers', compact('teachers'));
    }

    public function storeTeacher(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|unique:teachers',
            'email' => 'required|email|unique:users',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string',
            'specialization' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password123'),
            'phone' => $request->phone,
        ]);
        $user->assignRole('guru');

        Teacher::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'name' => $request->name,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'specialization' => $request->specialization,
        ]);

        return back()->with('success', 'Guru berhasil ditambahkan! Password default: password123');
    }

    public function updateTeacher(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'email' => 'required|email|unique:users,email,' . $teacher->user_id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:L,P',
            'specialization' => 'nullable|string|max:100',
        ]);

        $teacher->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $teacher->update([
            'name' => $request->name,
            'nip' => $request->nip,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'specialization' => $request->specialization,
        ]);

        return back()->with('success', 'Data guru berhasil diperbarui!');
    }

    public function deleteTeacher(Teacher $teacher)
    {
        $teacher->user->delete();
        $teacher->delete();
        return back()->with('success', 'Data guru berhasil dihapus!');
    }

    public function importTeachers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            Excel::import(new TeachersImport, $request->file('file'));
            return back()->with('success', 'Data guru berhasil diimpor!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    // === Classes ===
    public function classes()
    {
        $activeYear = AcademicYear::getActive();
        $classes = $activeYear
            ? ClassRoom::where('academic_year_id', $activeYear->id)->with(['homeroomTeacher', 'students'])->orderBy('name')->get()
            : collect();
        $teachers = Teacher::where('is_active', true)->orderBy('name')->get();

        return view('master.classes', compact('classes', 'teachers', 'activeYear'));
    }

    public function storeClass(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'grade_level' => 'required|in:7,8,9',
            'homeroom_teacher_id' => 'nullable|exists:teachers,id',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $activeYear = AcademicYear::getActive();
        if (!$activeYear) return back()->with('error', 'Tidak ada tahun ajaran aktif!');

        ClassRoom::create([
            'name' => $request->name,
            'grade_level' => $request->grade_level,
            'academic_year_id' => $activeYear->id,
            'homeroom_teacher_id' => $request->homeroom_teacher_id,
            'capacity' => $request->capacity ?? 32,
        ]);

        // Assign wali_kelas role
        if ($request->homeroom_teacher_id) {
            $teacher = Teacher::find($request->homeroom_teacher_id);
            if ($teacher && $teacher->user) {
                $teacher->user->assignRole('wali_kelas');
            }
        }

        return back()->with('success', 'Kelas berhasil ditambahkan!');
    }

    // === Subjects ===
    public function subjects()
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        return view('master.subjects', compact('subjects'));
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:subjects',
            'grade_level' => 'required|in:7,8,9,all',
            'kkm' => 'required|integer|min:0|max:100',
            'hours_per_week' => 'required|integer|min:1',
        ]);

        Subject::create($request->only(['name', 'code', 'grade_level', 'kkm', 'hours_per_week']));
        return back()->with('success', 'Mata pelajaran berhasil ditambahkan!');
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:subjects,code,' . $subject->id,
            'kkm' => 'required|integer|min:0|max:100',
            'grade_level' => 'required|in:7,8,9,all',
            'hours_per_week' => 'required|integer|min:1',
        ]);

        $subject->update($request->only(['name', 'code', 'kkm', 'grade_level', 'hours_per_week']));

        return back()->with('success', 'Mata pelajaran berhasil diperbarui!');
    }

    public function deleteSubject(Subject $subject)
    {
        $subject->delete();
        return back()->with('success', 'Mata pelajaran berhasil dihapus!');
    }

    public function importSubjects(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt|max:10240'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\SubjectsImport, $request->file('file'));
            return back()->with('success', 'Data mata pelajaran berhasil diimpor!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    // === Schedules ===
    public function schedules(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $classes = $activeYear ? ClassRoom::where('academic_year_id', $activeYear->id)->orderBy('name')->get() : collect();
        $teachers = Teacher::where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        $schedules = collect();
        $selectedClass = null;

        if ($request->filled('class_room_id') && $activeYear) {
            $selectedClass = ClassRoom::find($request->class_room_id);
            $schedules = Schedule::where('class_room_id', $request->class_room_id)
                ->where('academic_year_id', $activeYear->id)
                ->with(['subject', 'teacher'])
                ->orderByRaw("FIELD(day, 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu')")
                ->orderBy('lesson_hour')
                ->get();
        }

        return view('master.schedules', compact('classes', 'teachers', 'subjects', 'schedules', 'selectedClass'));
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'class_room_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'day' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'lesson_hour' => 'required|integer|min:1|max:9',
        ]);

        $activeYear = AcademicYear::getActive();
        if (!$activeYear) return back()->with('error', 'Tidak ada tahun ajaran aktif!');

        Schedule::create([
            'class_room_id' => $request->class_room_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'academic_year_id' => $activeYear->id,
            'day' => $request->day,
            'lesson_hour' => $request->lesson_hour,
        ]);

        return back()->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function deleteSchedule(Schedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal berhasil dihapus!');
    }

    public function importSchedules(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt|max:10240'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\SchedulesImport, $request->file('file'));
            return back()->with('success', 'Data jadwal berhasil diimpor!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor jadwal: ' . $e->getMessage());
        }
    }

    // === WhatsApp ===
}
