<?php

namespace App\Http\Controllers;

use App\Models\ExamPlan;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class ExamPlanController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $activeYear = AcademicYear::getActive();
        $examPlans = ExamPlan::with(['subject', 'classRoom'])
            ->where('teacher_id', $teacher->id)
            ->where('academic_year_id', $activeYear?->id)
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('exam-plans.index', compact('examPlans'));
    }

    public function create()
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $activeYear = AcademicYear::getActive();
        if ($activeYear) {
            $classIds = \App\Models\Schedule::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $activeYear->id)
                ->pluck('class_room_id')->unique();
            $subjectIds = \App\Models\Schedule::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $activeYear->id)
                ->pluck('subject_id')->unique();
            
            $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name')->get();
            $classRooms = ClassRoom::whereIn('id', $classIds)->orderBy('name')->get();
        } else {
            $subjects = Subject::where('is_active', true)->get();
            $classRooms = ClassRoom::all();
        }

        return view('exam-plans.create', compact('subjects', 'classRooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:catatan_sikap,formatif,sts,sas,kokurikuler',
            'description' => 'nullable|string'
        ]);

        $teacher = auth()->user()->teacher;
        $activeYear = AcademicYear::getActive();

        if (!$activeYear) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        ExamPlan::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $request->subject_id,
            'class_room_id' => $request->class_room_id,
            'academic_year_id' => $activeYear->id,
            'title' => $request->title,
            'date' => $request->date,
            'type' => $request->type,
            'description' => $request->description,
        ]);

        return redirect()->route('exam-plans.index')->with('success', 'Rencana ulangan berhasil ditambahkan.');
    }

    public function edit(ExamPlan $examPlan)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher || $examPlan->teacher_id !== $teacher->id) {
            return redirect()->route('exam-plans.index')->with('error', 'Akses ditolak.');
        }

        $subjects = Subject::all();
        $classRooms = ClassRoom::all();

        return view('exam-plans.edit', compact('examPlan', 'subjects', 'classRooms'));
    }

    public function update(Request $request, ExamPlan $examPlan)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher || $examPlan->teacher_id !== $teacher->id) {
            return redirect()->route('exam-plans.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:catatan_sikap,formatif,sts,sas,kokurikuler',
            'description' => 'nullable|string'
        ]);

        $examPlan->update($request->only([
            'subject_id', 'class_room_id', 'title', 'date', 'type', 'description'
        ]));

        return redirect()->route('exam-plans.index')->with('success', 'Rencana ulangan berhasil diperbarui.');
    }

    public function destroy(ExamPlan $examPlan)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher || $examPlan->teacher_id !== $teacher->id) {
            return redirect()->route('exam-plans.index')->with('error', 'Akses ditolak.');
        }

        $examPlan->delete();
        return redirect()->route('exam-plans.index')->with('success', 'Rencana ulangan berhasil dihapus.');
    }
}
