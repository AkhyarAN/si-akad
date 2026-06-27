<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $user = Auth::user();

        $query = Grade::with(['student', 'subject', 'classRoom', 'teacher'])
            ->where('academic_year_id', $activeYear?->id);

        if ($user->hasRole('guru') && !$user->hasRole('admin')) {
            $teacher = $user->teacher;
            if ($teacher) {
                $query->where('teacher_id', $teacher->id);
            }
        }

        if ($request->filled('class_room_id')) {
            $query->where('class_room_id', $request->class_room_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $grades = $query->latest()->paginate(20);

        $classes = $activeYear ? ClassRoom::where('academic_year_id', $activeYear->id)->orderBy('name')->get() : collect();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        return view('grades.index', compact('grades', 'classes', 'subjects'));
    }

    public function create()
    {
        $activeYear = AcademicYear::getActive();
        $classes = $activeYear ? ClassRoom::where('academic_year_id', $activeYear->id)->orderBy('name')->get() : collect();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        return view('grades.create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_room_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'type' => 'required|in:tugas,ulangan_harian,uts,uas,praktik',
            'description' => 'nullable|string|max:255',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.score' => 'required|numeric|min:0|max:100',
        ]);

        $user = Auth::user();
        $teacher = $user->teacher;
        $activeYear = AcademicYear::getActive();

        if (!$teacher || !$activeYear) {
            return back()->with('error', 'Data guru atau tahun ajaran tidak ditemukan.');
        }

        $subject = Subject::findOrFail($request->subject_id);

        DB::beginTransaction();
        try {
            foreach ($request->grades as $item) {
                $grade = Grade::create([
                    'student_id' => $item['student_id'],
                    'subject_id' => $request->subject_id,
                    'teacher_id' => $teacher->id,
                    'class_room_id' => $request->class_room_id,
                    'academic_year_id' => $activeYear->id,
                    'type' => $request->type,
                    'description' => $request->description,
                    'score' => $item['score'],
                    'max_score' => 100,
                ]);

                // Notify parents if score below KKM
                if ($item['score'] < $subject->kkm) {
                    try {
                        $whatsApp = new WhatsAppService();
                        $whatsApp->sendGradeNotification($grade);
                    } catch (\Exception $e) {
                        // Don't fail if WhatsApp fails
                    }
                }
            }

            DB::commit();
            return redirect()->route('grades.index')
                ->with('success', 'Nilai berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }

    public function report(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $classes = $activeYear ? ClassRoom::where('academic_year_id', $activeYear->id)->orderBy('name')->get() : collect();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        $reportData = null;
        $selectedClass = null;
        $selectedSubject = null;

        if ($request->filled('class_room_id') && $request->filled('subject_id')) {
            $selectedClass = ClassRoom::findOrFail($request->class_room_id);
            $selectedSubject = Subject::findOrFail($request->subject_id);

            $students = Student::where('class_room_id', $request->class_room_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            $reportData = [];
            foreach ($students as $student) {
                $grades = Grade::where('student_id', $student->id)
                    ->where('subject_id', $request->subject_id)
                    ->where('academic_year_id', $activeYear?->id)
                    ->get();

                $gradesByType = $grades->groupBy('type')->map(function ($items) {
                    return round($items->avg('score'), 1);
                });

                $reportData[] = [
                    'student' => $student,
                    'tugas' => $gradesByType['tugas'] ?? '-',
                    'ulangan_harian' => $gradesByType['ulangan_harian'] ?? '-',
                    'uts' => $gradesByType['uts'] ?? '-',
                    'uas' => $gradesByType['uas'] ?? '-',
                    'praktik' => $gradesByType['praktik'] ?? '-',
                    'average' => $grades->count() > 0 ? round($grades->avg('score'), 1) : '-',
                    'below_kkm' => $grades->count() > 0 && round($grades->avg('score'), 1) < $selectedSubject->kkm,
                ];
            }

            // Sort by average (ranking)
            usort($reportData, function ($a, $b) {
                if ($a['average'] === '-') return 1;
                if ($b['average'] === '-') return -1;
                return $b['average'] <=> $a['average'];
            });
        }

        return view('grades.report', compact('classes', 'subjects', 'reportData', 'selectedClass', 'selectedSubject'));
    }
}
