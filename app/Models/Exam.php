<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'title',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }


    public function questions()
    {
        return $this->hasMany(Question::class);
    }


}
