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
        // Teacher Attendance Report
        Route::get('/teacher-attendances/report', [\App\Http\Controllers\TeacherAttendanceController::class, 'report'])->name('teacher-attendances.report');

        // Master Data
        Route::middleware(['role:admin'])->prefix('master')->name('master.')->group(function () {
            // Dapodik Sync
            Route::prefix('dapodik')->group(function () {
                Route::get('/', [App\Http\Controllers\DapodikController::class, 'index'])->name('dapodik');
                Route::post('/save', [App\Http\Controllers\DapodikController::class, 'saveSettings'])->name('dapodik.save');
                Route::post('/test', [App\Http\Controllers\DapodikController::class, 'testConnection'])->name('dapodik.test');
                Route::post('/sync-ptk', [App\Http\Controllers\DapodikController::class, 'syncPTK'])->name('dapodik.ptk');
                Route::post('/sync-pd', [App\Http\Controllers\DapodikController::class, 'syncPesertaDidik'])->name('dapodik.pd');
                Route::post('/sync-rombel', [App\Http\Controllers\DapodikController::class, 'syncRombonganBelajar'])->name('dapodik.rombel');
            });

            // Backup & Restore
            Route::get('/backups', [App\Http\Controllers\BackupController::class, 'index'])->name('backups');
            Route::post('/backups/create', [App\Http\Controllers\BackupController::class, 'create'])->name('backups.create');
            Route::get('/backups/download/{filename}', [App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
            Route::post('/backups/restore/{filename}', [App\Http\Controllers\BackupController::class, 'restore'])->name('backups.restore');
            Route::delete('/backups/{filename}', [App\Http\Controllers\BackupController::class, 'destroy'])->name('backups.delete');
        });

        // Academic Years
        Route::get('/master/academic-years', [MasterDataController::class, 'academicYears'])->name('master.academic-years');
        Route::post('/master/academic-years', [MasterDataController::class, 'storeAcademicYear'])->name('master.academic-years.store');
        Route::put('/master/academic-years/{academicYear}/activate', [MasterDataController::class, 'setActiveYear'])->name('master.academic-years.activate');

        // Teachers
        Route::get('/master/teachers', [MasterDataController::class, 'teachers'])->name('master.teachers');
        Route::post('/master/teachers', [MasterDataController::class, 'storeTeacher'])->name('master.teachers.store');
        Route::put('/master/teachers/{teacher}', [MasterDataController::class, 'updateTeacher'])->name('master.teachers.update');
        Route::delete('/master/teachers/{teacher}', [MasterDataController::class, 'deleteTeacher'])->name('master.teachers.delete');
        Route::post('/master/teachers/import', [MasterDataController::class, 'importTeachers'])->name('master.teachers.import');

        // Classes
        Route::get('/master/classes', [MasterDataController::class, 'classes'])->name('master.classes');
        Route::post('/master/classes', [MasterDataController::class, 'storeClass'])->name('master.classes.store');

        // Subjects
        Route::get('/master/subjects', [MasterDataController::class, 'subjects'])->name('master.subjects');
        Route::post('/master/subjects', [MasterDataController::class, 'storeSubject'])->name('master.subjects.store');
        Route::put('/master/subjects/{subject}', [MasterDataController::class, 'updateSubject'])->name('master.subjects.update');
        Route::delete('/master/subjects/{subject}', [MasterDataController::class, 'deleteSubject'])->name('master.subjects.delete');
        Route::post('/master/subjects/import', [MasterDataController::class, 'importSubjects'])->name('master.subjects.import');

        // Schedules
        Route::get('/master/schedules', [MasterDataController::class, 'schedules'])->name('master.schedules');
        Route::post('/master/schedules', [MasterDataController::class, 'storeSchedule'])->name('master.schedules.store');
        Route::post('/master/schedules/import', [MasterDataController::class, 'importSchedules'])->name('master.schedules.import');
        Route::delete('/master/schedules/{schedule}', [MasterDataController::class, 'deleteSchedule'])->name('master.schedules.delete');

        // WhatsApp
        Route::get('/master/whatsapp', [\App\Http\Controllers\WhatsAppController::class, 'index'])->name('master.whatsapp');
        Route::post('/master/whatsapp/settings', [\App\Http\Controllers\WhatsAppController::class, 'updateSettings'])->name('master.whatsapp.settings');
        Route::post('/master/whatsapp/test', [\App\Http\Controllers\WhatsAppController::class, 'sendTest'])->name('master.whatsapp.test');
        Route::post('/master/whatsapp/broadcast', [\App\Http\Controllers\WhatsAppController::class, 'sendBroadcast'])->name('master.whatsapp.broadcast');

        // App Settings
        Route::get('/master/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('master.settings');
        Route::post('/master/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('master.settings.update');
    });

    // === STUDENTS (Admin, Guru, Kepsek) ===
    Route::middleware('role:admin|kepala_sekolah|guru|wali_kelas')->group(function () {
        Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
        Route::resource('students', StudentController::class);
    });

    // === TEACHER ATTENDANCE & EXAM PLANS (Guru) ===
    Route::middleware('role:guru|wali_kelas')->group(function () {
        Route::get('/teacher-attendances', [\App\Http\Controllers\TeacherAttendanceController::class, 'index'])->name('teacher-attendances.index');
        
        Route::resource('exam-plans', \App\Http\Controllers\ExamPlanController::class)->except(['show']);
    });

    // === HOMEROOM (Wali Kelas) ===
    Route::middleware('role:wali_kelas')->group(function () {
        Route::get('/homeroom', [\App\Http\Controllers\HomeroomController::class, 'index'])->name('homeroom.index');
    });

    // === ATTENDANCE (Admin, Guru, Kepsek) ===
    Route::middleware('role:admin|guru|wali_kelas|kepala_sekolah')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    });
    Route::middleware('role:admin|guru|wali_kelas')->group(function () {
        Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/students', [AttendanceController::class, 'getStudents'])->name('attendance.students');
    });
    Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report')
        ->middleware('role:admin|kepala_sekolah|guru|wali_kelas');

    // === GRADES (Admin, Guru, Kepsek) ===
    Route::middleware('role:admin|guru|wali_kelas|kepala_sekolah')->group(function () {
        Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
    });
    Route::middleware('role:admin|guru|wali_kelas')->group(function () {
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
