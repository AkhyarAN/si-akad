<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function parentProfile()
    {
        return $this->hasOne(ParentModel::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'author_id');
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isKepalaSekolah()
    {
        return $this->hasRole('kepala_sekolah');
    }

    public function isGuru()
    {
        return $this->hasRole('guru');
    }

    public function isWaliKelas()
    {
        return $this->hasRole('wali_kelas');
    }

    public function isOrangTua()
    {
        return $this->hasRole('orang_tua');
    }
}
