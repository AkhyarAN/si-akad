<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_room_id',
        'subject_id',
        'teacher_id',
        'academic_year_id',
        'day',
        'lesson_hour',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getTimeRangeAttribute()
    {
        return 'Jam Ke-' . $this->lesson_hour;
    }
}
