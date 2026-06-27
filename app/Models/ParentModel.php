<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'whatsapp_number',
        'relationship',
        'occupation',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }

    public function attendanceNotifications()
    {
        return $this->hasMany(AttendanceNotification::class, 'parent_id');
    }
}
