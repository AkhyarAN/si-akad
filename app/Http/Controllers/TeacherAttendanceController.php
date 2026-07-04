<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\TeacherAttendance;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherAttendanceController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak terdaftar sebagai guru.');
        }

        $activeYear = AcademicYear::getActive();
        $attendances = TeacherAttendance::with(['classRoom', 'subject', 'schedule'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->paginate(15);

        return view('teacher-attendance.index', compact('attendances'));
    }

    public function report(Request $request)
    {
        $teachers = Teacher::where('is_active', true)->orderBy('name')->get();
        $reportData = null;

        if ($request->filled('month') && $request->filled('year')) {
            $query = TeacherAttendance::with(['teacher', 'classRoom', 'subject', 'schedule'])
                ->whereMonth('date', $request->month)
                ->whereYear('date', $request->year);
                
            if ($request->filled('teacher_id') && $request->teacher_id !== 'all') {
                $query->where('teacher_id', $request->teacher_id);
            }
            
            $reportData = $query->orderBy('date', 'desc')
                ->orderBy('time_in', 'desc')
                ->get();
        }

        return view('teacher-attendance.report', compact('teachers', 'reportData'));
    }
}
