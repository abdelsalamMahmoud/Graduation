<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'video_path',
        'order',
    ];

    public $timestamps = true ;

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
