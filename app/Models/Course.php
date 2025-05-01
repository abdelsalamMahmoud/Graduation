<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;
    protected $fillable =[
        'teacher_id',
        'title',
        'description',
        'status',
        'cover_image'
    ];

    public $timestamps = true ;

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
 
}
