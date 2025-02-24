<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'teacher_id',
        'student_id',
        'date',
        'time',
        'session_time',
        'status',
        'feedback',
    ];

    public $timestamps = true;
}
