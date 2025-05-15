<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'fullName',
        'email',
        'password',
        'role',
        'verification_code',
        'verification_expires_at',
        'is_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function teacherinfo(): HasOne
    {
        return $this->hasOne(TeacherProfile::class,'user_id','id');
    }

    // For a student: get the teacher(s) they subscribed to
    public function subscribedTeachers()
    {
        return $this->belongsToMany(User::class, 'schedules', 'student_id', 'teacher_id')
            ->wherePivot('status', 'approved');
    }

// For a teacher: get the students subscribed to them
    public function subscribedStudents()
    {
        return $this->belongsToMany(User::class, 'schedules', 'teacher_id', 'student_id')
            ->wherePivot('status', 'approved');
    }

    public function plans():HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function examsTaken()
    {
        return $this->belongsToMany(Exam::class, 'student_exams', 'student_id', 'exam_id')
            ->withPivot('score');
    }

    public function feedbacks():HasMany
    {
        return $this->hasMany(Rate::class,'teacher_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
