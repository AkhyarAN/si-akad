<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'nisn',
        'name',
        'gender',
        'birth_date',
        'birth_place',
        'religion',
        'address',
        'phone',
        'class_room_id',
        'parent_id',
        'photo',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function getAttendancePercentage($month = null, $year = null)
    {
        $query = $this->attendances();

        if ($month && $year) {
            $query->whereMonth('date', $month)->whereYear('date', $year);
        }

        $total = $query->count();
        if ($total === 0) return 100;

        $hadir = $query->where('status', 'hadir')->count();
        return round(($hadir / $total) * 100, 1);
    }

    public function getAverageGrade($subjectId = null, $academicYearId = null)
    {
        $query = $this->grades();

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        return round($query->avg('score') ?? 0, 1);
    }
}
