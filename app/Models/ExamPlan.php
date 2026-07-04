<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'class_room_id',
        'academic_year_id',
        'title',
        'date',
        'type',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'catatan_sikap' => 'Catatan Sikap',
            'formatif' => 'Asesmen Formatif',
            'sts' => 'Sumatif Tengah Semester',
            'sas' => 'Sumatif Akhir Semester',
            'kokurikuler' => 'Kokurikuler',
            default => $this->type,
        };
    }
}
