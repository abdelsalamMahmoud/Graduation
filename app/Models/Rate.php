<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rate extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'teacher_id',
        'rate',
        'feedback'
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function teacherProfile():BelongsTo
    {
        return $this->BelongsTo(TeacherProfile::class, 'teacher_id', 'user_id');
    }
}