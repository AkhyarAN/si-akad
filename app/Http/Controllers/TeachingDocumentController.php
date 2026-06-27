<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\TeachingDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TeachingDocumentController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $user = Auth::user();

        $query = TeachingDocument::with(['teacher', 'subject', 'classRoom'])
            ->where('academic_year_id', $activeYear?->id);

        if ($user->hasRole('guru') && !$user->hasRole('admin') && !$user->hasRole('kepala_sekolah')) {
            $teacher = $user->teacher;
            if ($teacher) {
                $query->where('teacher_id', $teacher->id);
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $documents = $query->latest()->paginate(15);
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        return view('teaching-documents.index', compact('documents', 'subjects'));
    }

    public function create()
    {
        $activeYear = AcademicYear::getActive();
        $classes = $activeYear ? ClassRoom::where('academic_year_id', $activeYear->id)->orderBy('name')->get() : collect();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        return view('teaching-documents.create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'class_room_id' => 'nullable|exists:class_rooms,id',
            'type' => 'required|in:rpp,silabus,prota,prosem,kkm,lainnya',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ]);

        $user = Auth::user();
        $teacher = $user->teacher;
        $activeYear = AcademicYear::getActive();

        if (!$teacher || !$activeYear) {
            return back()->with('error', 'Data guru atau tahun ajaran tidak ditemukan.');
        }

        $file = $request->file('file');
        $filePath = $file->store('teaching-documents/' . $activeYear->year, 'public');

        TeachingDocument::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $request->subject_id,
            'class_room_id' => $request->class_room_id,
            'academic_year_id' => $activeYear->id,
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'status' => 'draft',
        ]);

        return redirect()->route('teaching-documents.index')
            ->with('success', 'Dokumen berhasil diupload!');
    }

    public function show(TeachingDocument $teachingDocument)
    {
        $teachingDocument->load(['teacher', 'subject', 'classRoom', 'reviewer']);
        return view('teaching-documents.show', compact('teachingDocument'));
    }

    public function submit(TeachingDocument $teachingDocument)
    {
        $teachingDocument->update(['status' => 'submitted']);
        return back()->with('success', 'Dokumen berhasil diajukan untuk review!');
    }

    public function review(Request $request, TeachingDocument $teachingDocument)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'review_notes' => 'nullable|string',
        ]);

        $teachingDocument->update([
            'status' => $request->status,
            'review_notes' => $request->review_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $message = $request->status === 'approved' ? 'Dokumen disetujui!' : 'Dokumen ditolak!';
        return back()->with('success', $message);
    }

    public function download(TeachingDocument $teachingDocument)
    {
        return Storage::disk('public')->download($teachingDocument->file_path, $teachingDocument->file_name);
    }

    public function destroy(TeachingDocument $teachingDocument)
    {
        Storage::disk('public')->delete($teachingDocument->file_path);
        $teachingDocument->delete();

        return redirect()->route('teaching-documents.index')
            ->with('success', 'Dokumen berhasil dihapus!');
    }
}
