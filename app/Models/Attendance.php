<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'schedule_id',
        'class_room_id',
        'subject_id',
        'teacher_id',
        'date',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

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

    public function notifications()
    {
        return $this->hasMany(AttendanceNotification::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'hadir' => '<span class="badge bg-success">Hadir</span>',
            'izin' => '<span class="badge bg-info">Izin</span>',
            'sakit' => '<span class="badge bg-warning">Sakit</span>',
            'alpha' => '<span class="badge bg-danger">Alpha</span>',
            default => '<span class="badge bg-secondary">-</span>',
        };
    }
}
