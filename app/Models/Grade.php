<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'teacher_id',
        'class_room_id',
        'academic_year_id',
        'type',
        'description',
        'score',
        'max_score',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
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
            'tugas' => 'Tugas',
            'ulangan_harian' => 'Ulangan Harian',
            'uts' => 'UTS',
            'uas' => 'UAS',
            'praktik' => 'Praktik',
            default => $this->type,
        };
    }

    public function getPercentageAttribute()
    {
        if ($this->max_score == 0) return 0;
        return round(($this->score / $this->max_score) * 100, 1);
    }
}
