<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'grade_level',
        'academic_year_id',
        'homeroom_teacher_id',
        'capacity',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function homeroomTeacher()
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_room_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'class_room_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_room_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'class_room_id');
    }

    public function getStudentCountAttribute()
    {
        return $this->students()->count();
    }

    public function getFullNameAttribute()
    {
        return 'Kelas ' . $this->name;
    }
}
