<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'semester',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function classRooms()
    {
        return $this->hasMany(ClassRoom::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function teachingDocuments()
    {
        return $this->hasMany(TeachingDocument::class);
    }

    public static function getActive()
    {
        return static::where('is_active', true)->first();
    }

    public function getFullNameAttribute()
    {
        return $this->year . ' - Semester ' . ucfirst($this->semester);
    }
}
