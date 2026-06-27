<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeachingDocumentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest routes
Route::get('/', function () {
    if (Auth::check()) return redirect()->route('dashboard');
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', function () {
    $credentials = request()->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials, request()->boolean('remember'))) {
        request()->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
})->name('login.post')->middleware('guest');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // === ADMIN & KEPALA SEKOLAH ROUTES ===
    Route::middleware('role:admin|kepala_sekolah')->group(function () {
        // Academic Years
        Route::get('/master/academic-years', [MasterDataController::class, 'academicYears'])->name('master.academic-years');
        Route::post('/master/academic-years', [MasterDataController::class, 'storeAcademicYear'])->name('master.academic-years.store');
        Route::put('/master/academic-years/{academicYear}/activate', [MasterDataController::class, 'setActiveYear'])->name('master.academic-years.activate');

        // Teachers
        Route::get('/master/teachers', [MasterDataController::class, 'teachers'])->name('master.teachers');
        Route::post('/master/teachers', [MasterDataController::class, 'storeTeacher'])->name('master.teachers.store');

        // Classes
        Route::get('/master/classes', [MasterDataController::class, 'classes'])->name('master.classes');
        Route::post('/master/classes', [MasterDataController::class, 'storeClass'])->name('master.classes.store');

        // Subjects
        Route::get('/master/subjects', [MasterDataController::class, 'subjects'])->name('master.subjects');
        Route::post('/master/subjects', [MasterDataController::class, 'storeSubject'])->name('master.subjects.store');

        // Schedules
        Route::get('/master/schedules', [MasterDataController::class, 'schedules'])->name('master.schedules');
        Route::post('/master/schedules', [MasterDataController::class, 'storeSchedule'])->name('master.schedules.store');
        Route::delete('/master/schedules/{schedule}', [MasterDataController::class, 'deleteSchedule'])->name('master.schedules.delete');
    });

    // === STUDENTS (Admin, Guru, Kepsek) ===
    Route::middleware('role:admin|kepala_sekolah|guru|wali_kelas')->group(function () {
        Route::resource('students', StudentController::class);
    });

    // === ATTENDANCE (Admin, Guru) ===
    Route::middleware('role:admin|guru|wali_kelas')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/students', [AttendanceController::class, 'getStudents'])->name('attendance.students');
    });
    Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report')
        ->middleware('role:admin|kepala_sekolah|guru|wali_kelas');

    // === GRADES (Admin, Guru) ===
    Route::middleware('role:admin|guru|wali_kelas')->group(function () {
        Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
        Route::get('/grades/create', [GradeController::class, 'create'])->name('grades.create');
        Route::post('/grades', [GradeController::class, 'store'])->name('grades.store');
    });
    Route::get('/grades/report', [GradeController::class, 'report'])->name('grades.report')
        ->middleware('role:admin|kepala_sekolah|guru|wali_kelas|orang_tua');

    // === TEACHING DOCUMENTS (Admin, Guru, Kepsek) ===
    Route::middleware('role:admin|guru|wali_kelas|kepala_sekolah')->group(function () {
        Route::get('/teaching-documents', [TeachingDocumentController::class, 'index'])->name('teaching-documents.index');
        Route::get('/teaching-documents/create', [TeachingDocumentController::class, 'create'])->name('teaching-documents.create');
        Route::post('/teaching-documents', [TeachingDocumentController::class, 'store'])->name('teaching-documents.store');
        Route::get('/teaching-documents/{teachingDocument}', [TeachingDocumentController::class, 'show'])->name('teaching-documents.show');
        Route::put('/teaching-documents/{teachingDocument}/submit', [TeachingDocumentController::class, 'submit'])->name('teaching-documents.submit');
        Route::get('/teaching-documents/{teachingDocument}/download', [TeachingDocumentController::class, 'download'])->name('teaching-documents.download');
        Route::delete('/teaching-documents/{teachingDocument}', [TeachingDocumentController::class, 'destroy'])->name('teaching-documents.destroy');
    });

    // Review (only kepala_sekolah and admin)
    Route::put('/teaching-documents/{teachingDocument}/review', [TeachingDocumentController::class, 'review'])
        ->name('teaching-documents.review')
        ->middleware('role:admin|kepala_sekolah');

    // === API Endpoints ===
    Route::get('/api/attendance-chart', function () {
        $data = \App\Models\Attendance::whereDate('date', today())
            ->select('status', \DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
        return response()->json($data);
    })->name('api.attendance-chart');

    Route::get('/api/grade-chart', function () {
        $activeYear = \App\Models\AcademicYear::getActive();
        $data = \App\Models\Grade::where('academic_year_id', $activeYear?->id)
            ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
            ->select('subjects.name', \DB::raw('ROUND(AVG(grades.score), 1) as avg_score'))
            ->groupBy('subjects.name')
            ->get();
        return response()->json($data);
    })->name('api.grade-chart');
});
