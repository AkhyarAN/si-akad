<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $query = Student::with(['classRoom', 'parent'])->where('is_active', true);

        if ($request->filled('class_room_id')) {
            $query->where('class_room_id', $request->class_room_id);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->orderBy('name')->paginate(20);
        $classes = $activeYear ? ClassRoom::where('academic_year_id', $activeYear->id)->orderBy('name')->get() : collect();

        return view('students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $activeYear = AcademicYear::getActive();
        $classes = $activeYear ? ClassRoom::where('academic_year_id', $activeYear->id)->orderBy('name')->get() : collect();
        $parents = ParentModel::orderBy('name')->get();

        return view('students.create', compact('classes', 'parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:students',
            'nisn' => 'nullable|unique:students',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'religion' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'class_room_id' => 'nullable|exists:class_rooms,id',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'parent_whatsapp' => 'nullable|string|max:20',
            'parent_relationship' => 'nullable|in:ayah,ibu,wali',
        ]);

        DB::beginTransaction();
        try {
            // Create parent if info provided
            $parentId = null;
            if ($request->filled('parent_name') && $request->filled('parent_whatsapp')) {
                $parentUser = User::create([
                    'name' => $request->parent_name,
                    'email' => 'parent_' . $request->nis . '@siakad.local',
                    'password' => Hash::make('password123'),
                ]);
                $parentUser->assignRole('orang_tua');

                $parent = ParentModel::create([
                    'user_id' => $parentUser->id,
                    'name' => $request->parent_name,
                    'phone' => $request->parent_phone,
                    'whatsapp_number' => $request->parent_whatsapp,
                    'relationship' => $request->parent_relationship ?? 'ayah',
                ]);
                $parentId = $parent->id;
            }

            Student::create([
                'nis' => $request->nis,
                'nisn' => $request->nisn,
                'name' => $request->name,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'birth_place' => $request->birth_place,
                'religion' => $request->religion,
                'address' => $request->address,
                'class_room_id' => $request->class_room_id,
                'parent_id' => $parentId,
            ]);

            DB::commit();
            return redirect()->route('students.index')->with('success', 'Siswa berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan siswa: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Student $student)
    {
        $student->load(['classRoom', 'parent', 'attendances', 'grades.subject']);
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $activeYear = AcademicYear::getActive();
        $classes = $activeYear ? ClassRoom::where('academic_year_id', $activeYear->id)->orderBy('name')->get() : collect();
        $parents = ParentModel::orderBy('name')->get();

        return view('students.edit', compact('student', 'classes', 'parents'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nis' => 'required|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'class_room_id' => 'nullable|exists:class_rooms,id',
        ]);

        $student->update($request->only([
            'nis', 'nisn', 'name', 'gender', 'birth_date', 'birth_place',
            'religion', 'address', 'phone', 'class_room_id', 'parent_id',
        ]));

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy(Student $student)
    {
        $student->update(['is_active' => false]);
        return redirect()->route('students.index')->with('success', 'Siswa berhasil dinonaktifkan!');
    }
}
