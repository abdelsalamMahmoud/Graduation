<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verse extends Model
{
    use HasFactory;

    protected $fillable = [
        'surah_id', 'verse_number', 'text',
    ];

    public function surah()
    {
        return $this->belongsTo(Surah::class);
    }

}
