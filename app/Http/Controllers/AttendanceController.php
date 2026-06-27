<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use App\Jobs\SendWhatsAppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $query = Attendance::with(['student', 'subject', 'classRoom', 'teacher']);

        // Filters
        if ($request->filled('class_room_id')) {
            $query->where('class_room_id', $request->class_room_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', today());
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Guru hanya melihat absensi yang dia input
        $user = Auth::user();
        if ($user->hasRole('guru') && !$user->hasRole('admin')) {
            $teacher = $user->teacher;
            if ($teacher) {
                $query->where('teacher_id', $teacher->id);
            }
        }

        $attendances = $query->latest()->paginate(20);

        $classes = $activeYear ? ClassRoom::where('academic_year_id', $activeYear->id)->orderBy('name')->get() : collect();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        return view('attendance.index', compact('attendances', 'classes', 'subjects'));
    }

    public function create()
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        $activeYear = AcademicYear::getActive();

        if (!$teacher || !$activeYear) {
            return redirect()->route('attendance.index')
                ->with('error', 'Data guru atau tahun ajaran tidak ditemukan.');
        }

        // Get teacher's schedule for today
        $dayMap = ['sunday' => 'minggu', 'monday' => 'senin', 'tuesday' => 'selasa', 'wednesday' => 'rabu', 'thursday' => 'kamis', 'friday' => 'jumat', 'saturday' => 'sabtu'];
        $today = $dayMap[strtolower(now()->format('l'))] ?? 'senin';

        $schedules = Schedule::where('teacher_id', $teacher->id)
            ->where('academic_year_id', $activeYear->id)
            ->where('day', $today)
            ->with(['classRoom', 'subject'])
            ->orderBy('start_time')
            ->get();

        // Also get all classes for manual selection
        $classes = ClassRoom::where('academic_year_id', $activeYear->id)->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        return view('attendance.create', compact('schedules', 'classes', 'subjects', 'teacher'));
    }

    public function getStudents(Request $request)
    {
        $students = Student::where('class_room_id', $request->class_room_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Check existing attendance for today
        $existing = Attendance::where('class_room_id', $request->class_room_id)
            ->where('subject_id', $request->subject_id)
            ->whereDate('date', $request->date ?? today())
            ->pluck('status', 'student_id')
            ->toArray();

        return response()->json([
            'students' => $students,
            'existing' => $existing,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_room_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:hadir,izin,sakit,alpha',
        ]);

        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return back()->with('error', 'Data guru tidak ditemukan.');
        }

        // Find schedule
        $schedule = Schedule::where('class_room_id', $request->class_room_id)
            ->where('subject_id', $request->subject_id)
            ->where('teacher_id', $teacher->id)
            ->first();

        DB::beginTransaction();
        try {
            $notifyAttendances = [];

            foreach ($request->attendance as $item) {
                $attendance = Attendance::updateOrCreate(
                    [
                        'student_id' => $item['student_id'],
                        'subject_id' => $request->subject_id,
                        'date' => $request->date,
                    ],
                    [
                        'schedule_id' => $schedule?->id,
                        'class_room_id' => $request->class_room_id,
                        'teacher_id' => $teacher->id,
                        'status' => $item['status'],
                        'notes' => $item['notes'] ?? null,
                    ]
                );

                // Queue WhatsApp notification for non-hadir
                if ($item['status'] !== 'hadir') {
                    $notifyAttendances[] = $attendance;
                }
            }

            DB::commit();

            // Dispatch WhatsApp notifications
            foreach ($notifyAttendances as $att) {
                SendWhatsAppNotification::dispatch($att);
            }

            return redirect()->route('attendance.index')
                ->with('success', 'Absensi berhasil disimpan! Notifikasi WhatsApp telah dikirim ke orang tua siswa yang tidak hadir.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }

    public function report(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $classes = $activeYear ? ClassRoom::where('academic_year_id', $activeYear->id)->orderBy('name')->get() : collect();

        $reportData = null;

        if ($request->filled('class_room_id') && $request->filled('month') && $request->filled('year')) {
            $classRoom = ClassRoom::with('students')->findOrFail($request->class_room_id);
            $students = $classRoom->students()->where('is_active', true)->orderBy('name')->get();

            $reportData = [];
            foreach ($students as $student) {
                $attendances = Attendance::where('student_id', $student->id)
                    ->whereMonth('date', $request->month)
                    ->whereYear('date', $request->year)
                    ->select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->toArray();

                $reportData[] = [
                    'student' => $student,
                    'hadir' => $attendances['hadir'] ?? 0,
                    'izin' => $attendances['izin'] ?? 0,
                    'sakit' => $attendances['sakit'] ?? 0,
                    'alpha' => $attendances['alpha'] ?? 0,
                    'total' => array_sum($attendances),
                    'percentage' => array_sum($attendances) > 0
                        ? round((($attendances['hadir'] ?? 0) / array_sum($attendances)) * 100, 1)
                        : 0,
                ];
            }
        }

        return view('attendance.report', compact('classes', 'reportData'));
    }
}
