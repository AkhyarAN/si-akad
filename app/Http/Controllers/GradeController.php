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

        if ($user->hasRole('guru') && !$user->hasRole('admin') && $teacher) {
            $classIds = \App\Models\Schedule::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $activeYear?->id)
                ->pluck('class_room_id')->unique();
            $subjectIds = \App\Models\Schedule::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $activeYear?->id)
                ->pluck('subject_id')->unique();

            $classes = $activeYear ? ClassRoom::whereIn('id', $classIds)->orderBy('name')->get() : collect();
            $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name')->get();
        } else {
            $classes = $activeYear ? ClassRoom::where('academic_year_id', $activeYear->id)->orderBy('name')->get() : collect();
            $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        }

        return view('grades.index', compact('grades', 'classes', 'subjects'));
    }

    public function create()
    {
        $activeYear = AcademicYear::getActive();
        $user = Auth::user();
        $teacher = $user->teacher ?? null;
        $examPlans = collect();

        if ($user->hasRole('guru') && !$user->hasRole('admin') && $teacher) {
            $classIds = \App\Models\Schedule::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $activeYear?->id)
                ->pluck('class_room_id')->unique();
            $subjectIds = \App\Models\Schedule::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $activeYear?->id)
                ->pluck('subject_id')->unique();

            $classes = $activeYear ? ClassRoom::whereIn('id', $classIds)->orderBy('name')->get() : collect();
            $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name')->get();
            
            $examPlans = \App\Models\ExamPlan::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $activeYear?->id)
                ->orderBy('date', 'desc')
                ->get();
        } else {
            $classes = $activeYear ? ClassRoom::where('academic_year_id', $activeYear->id)->orderBy('name')->get() : collect();
            $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        }

        return view('grades.create', compact('classes', 'subjects', 'examPlans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_room_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'type' => 'required|in:catatan_sikap,formatif,sts,sas,kokurikuler',
            'description' => 'nullable|string|max:255',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.score' => 'nullable|numeric|min:0|max:100',
            'grades.*.notes' => 'nullable|string|max:1000',
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
                    'score' => $request->type === 'catatan_sikap' ? null : ($item['score'] ?? 0),
                    'notes' => $request->type === 'catatan_sikap' ? ($item['notes'] ?? null) : null,
                    'max_score' => 100,
                ]);

                // Notify parents if score below KKM
                if ($request->type !== 'catatan_sikap' && isset($item['score']) && $item['score'] < $subject->kkm) {
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
        $user = Auth::user();
        $teacher = $user->teacher;

        if ($user->hasRole('guru') && !$user->hasRole('admin') && $teacher) {
            $classIds = \App\Models\Schedule::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $activeYear?->id)
                ->pluck('class_room_id')->unique();
            $subjectIds = \App\Models\Schedule::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $activeYear?->id)
                ->pluck('subject_id')->unique();

            $classes = $activeYear ? ClassRoom::whereIn('id', $classIds)->orderBy('name')->get() : collect();
            $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name')->get();
        } else {
            $classes = $activeYear ? ClassRoom::where('academic_year_id', $activeYear->id)->orderBy('name')->get() : collect();
            $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        }

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

                $gradesByType = $grades->groupBy('type')->map(function ($items, $type) {
                    if ($type === 'catatan_sikap') {
                        return $items->pluck('notes')->filter()->join('; ');
                    }
                    return round($items->avg('score'), 1);
                });

                $reportData[] = [
                    'student' => $student,
                    'catatan_sikap' => $gradesByType['catatan_sikap'] ?? '-',
                    'formatif' => $gradesByType['formatif'] ?? '-',
                    'sts' => $gradesByType['sts'] ?? '-',
                    'sas' => $gradesByType['sas'] ?? '-',
                    'kokurikuler' => $gradesByType['kokurikuler'] ?? '-',
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
