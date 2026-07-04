<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeachingDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('kepala_sekolah')) {
            return $this->kepalaSekolahDashboard();
        } elseif ($user->hasRole('guru') || $user->hasRole('wali_kelas')) {
            return $this->guruDashboard();
        } elseif ($user->hasRole('orang_tua')) {
            return $this->orangTuaDashboard();
        }

        return redirect()->route('login');
    }

    protected function adminDashboard()
    {
        $activeYear = AcademicYear::getActive();

        $stats = [
            'total_students' => Student::where('is_active', true)->count(),
            'total_teachers' => Teacher::where('is_active', true)->count(),
            'total_classes' => $activeYear ? ClassRoom::where('academic_year_id', $activeYear->id)->count() : 0,
            'total_subjects' => \App\Models\Subject::where('is_active', true)->count(),
        ];

        // Attendance today
        $todayAttendance = Attendance::whereDate('date', today())
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Recent announcements
        $announcements = Announcement::with('author')
            ->latest()
            ->take(5)
            ->get();

        // Attendance trend (last 7 days)
        $attendanceTrend = Attendance::where('date', '>=', now()->subDays(7))
            ->select('date', 'status', DB::raw('count(*) as total'))
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        return view('dashboard.admin', compact('stats', 'todayAttendance', 'announcements', 'attendanceTrend', 'activeYear'));
    }

    protected function kepalaSekolahDashboard()
    {
        $activeYear = AcademicYear::getActive();

        $stats = [
            'total_students' => Student::where('is_active', true)->count(),
            'total_teachers' => Teacher::where('is_active', true)->count(),
            'total_classes' => $activeYear ? ClassRoom::where('academic_year_id', $activeYear->id)->count() : 0,
        ];

        // Attendance per class today
        $classAttendance = [];
        if ($activeYear) {
            $classes = ClassRoom::where('academic_year_id', $activeYear->id)->with('students')->get();
            foreach ($classes as $class) {
                $studentIds = $class->students->pluck('id');
                $todayAtt = Attendance::whereIn('student_id', $studentIds)
                    ->whereDate('date', today())
                    ->select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->toArray();

                $classAttendance[$class->name] = $todayAtt;
            }
        }

        // Average grades per subject
        $subjectGrades = Grade::where('academic_year_id', $activeYear?->id)
            ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
            ->select('subjects.name', DB::raw('ROUND(AVG(grades.score), 1) as avg_score'))
            ->groupBy('subjects.name')
            ->get();

        // Teaching documents status
        $documentStats = TeachingDocument::where('academic_year_id', $activeYear?->id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Low attendance students
        $lowAttendanceStudents = DB::table('attendances')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->join('class_rooms', 'students.class_room_id', '=', 'class_rooms.id')
            ->where('attendances.date', '>=', now()->subDays(30))
            ->where('attendances.status', '!=', 'hadir')
            ->select('students.name', 'class_rooms.name as class_name', DB::raw('count(*) as absent_count'))
            ->groupBy('students.id', 'students.name', 'class_rooms.name')
            ->having('absent_count', '>=', 3)
            ->orderByDesc('absent_count')
            ->take(10)
            ->get();

        return view('dashboard.kepala-sekolah', compact(
            'stats', 'classAttendance', 'subjectGrades', 'documentStats', 'lowAttendanceStudents', 'activeYear'
        ));
    }

    protected function guruDashboard()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return view('dashboard.guru', ['teacher' => null]);
        }

        $activeYear = AcademicYear::getActive();

        // Today's schedule
        $dayMap = ['sunday' => 'minggu', 'monday' => 'senin', 'tuesday' => 'selasa', 'wednesday' => 'rabu', 'thursday' => 'kamis', 'friday' => 'jumat', 'saturday' => 'sabtu'];
        $today = $dayMap[strtolower(now()->format('l'))] ?? 'senin';

        $todaySchedules = \App\Models\Schedule::where('teacher_id', $teacher->id)
            ->where('academic_year_id', $activeYear?->id)
            ->where('day', $today)
            ->with(['classRoom', 'subject'])
            ->orderBy('lesson_hour')
            ->get();

        // Recent grades
        $recentGrades = Grade::where('teacher_id', $teacher->id)
            ->with(['student', 'subject', 'classRoom'])
            ->latest()
            ->take(10)
            ->get();

        // Teaching documents
        $documents = TeachingDocument::where('teacher_id', $teacher->id)
            ->where('academic_year_id', $activeYear?->id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Homeroom class info
        $homeroomClass = $teacher->homeroomClass;
        $homeroomStudents = $homeroomClass ? $homeroomClass->students()->where('is_active', true)->count() : 0;

        return view('dashboard.guru', compact(
            'teacher', 'todaySchedules', 'recentGrades', 'documents', 'homeroomClass', 'homeroomStudents', 'activeYear'
        ));
    }

    protected function orangTuaDashboard()
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile;

        if (!$parentProfile) {
            return view('dashboard.orang-tua', ['parentProfile' => null]);
        }

        $students = $parentProfile->students()->with(['classRoom'])->where('is_active', true)->get();
        $activeYear = AcademicYear::getActive();

        $studentsData = [];
        foreach ($students as $student) {
            // Attendance this month
            $monthlyAttendance = Attendance::where('student_id', $student->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            // Recent grades
            $recentGrades = Grade::where('student_id', $student->id)
                ->where('academic_year_id', $activeYear?->id)
                ->with('subject')
                ->latest()
                ->take(5)
                ->get();

            // Average per subject
            $subjectAverages = Grade::where('student_id', $student->id)
                ->where('academic_year_id', $activeYear?->id)
                ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
                ->select('subjects.name', DB::raw('ROUND(AVG(grades.score), 1) as avg_score'))
                ->groupBy('subjects.name')
                ->get();

            // Upcoming Exams
            $upcomingExams = \App\Models\ExamPlan::where('class_room_id', $student->class_room_id)
                ->where('academic_year_id', $activeYear?->id)
                ->where('date', '>=', today())
                ->with('subject')
                ->orderBy('date', 'asc')
                ->take(3)
                ->get();

            $studentsData[] = [
                'student' => $student,
                'attendance' => $monthlyAttendance,
                'recentGrades' => $recentGrades,
                'subjectAverages' => $subjectAverages,
                'upcomingExams' => $upcomingExams,
            ];
        }

        return view('dashboard.orang-tua', compact('parentProfile', 'studentsData', 'activeYear'));
    }
}
