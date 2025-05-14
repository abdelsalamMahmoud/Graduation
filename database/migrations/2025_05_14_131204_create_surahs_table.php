<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surahs', function (Blueprint $table) {
            $table->id(); // Surah ID (1 to 114)
            $table->string('name'); // Arabic name
            $table->string('transliteration'); // e.g., Al-Fatihah
            $table->enum('type', ['meccan', 'medinan']);
            $table->unsignedSmallInteger('verse_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surahs');
    }
};
