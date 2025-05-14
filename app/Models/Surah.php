<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surah extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'name', 'transliteration', 'type', 'verse_count',
    ];

    public $incrementing = false; // Because ID comes from JSON

    public function verses()
    {
        return $this->hasMany(Verse::class);
    }

}
