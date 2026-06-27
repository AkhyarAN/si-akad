<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'name',
        'gender',
        'phone',
        'specialization',
        'address',
        'photo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function homeroomClass()
    {
        return $this->hasOne(ClassRoom::class, 'homeroom_teacher_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function teachingDocuments()
    {
        return $this->hasMany(TeachingDocument::class);
    }

    public function getTodaySchedules()
    {
        $day = strtolower(now()->locale('id')->dayName);
        $activeYear = AcademicYear::getActive();

        return $this->schedules()
            ->where('day', $day)
            ->where('academic_year_id', $activeYear?->id)
            ->with(['classRoom', 'subject'])
            ->orderBy('start_time')
            ->get();
    }
}
