<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeroomController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;
        
        if (!$teacher || !$teacher->homeroomClass) {
            return redirect()->route('dashboard')->with('error', 'Anda bukan wali kelas dari kelas manapun.');
        }

        $classRoom = $teacher->homeroomClass;
        $activeYear = AcademicYear::getActive();

        // Get month filter, default to current month
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $students = $classRoom->students()->where('is_active', true)->orderBy('name')->get();
        $studentIds = $students->pluck('id')->toArray();

        $studentsData = [];

        foreach ($students as $student) {
            // Attendance for selected month/year
            $attendance = Attendance::where('student_id', $student->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            // Average Grades for current academic year (across all subjects)
            $averageGrade = 0;
            if ($activeYear) {
                $avg = Grade::where('student_id', $student->id)
                    ->where('academic_year_id', $activeYear->id)
                    ->avg('score');
                $averageGrade = $avg ? round($avg, 2) : 0;
            }

            $studentsData[] = [
                'student' => $student,
                'attendance' => $attendance,
                'average_grade' => $averageGrade
            ];
        }

        return view('homeroom.index', compact('classRoom', 'studentsData', 'month', 'year', 'activeYear'));
    }
}
