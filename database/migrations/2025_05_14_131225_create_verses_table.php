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
        Schema::create('verses', function (Blueprint $table) {
            $table->id(); // Auto-increment
            $table->foreignId('surah_id')->constrained('surahs')->onDelete('cascade');
            $table->unsignedSmallInteger('verse_number'); // 1 to n
            $table->text('text'); // Arabic verse text
            $table->timestamps();
            $table->unique(['surah_id', 'verse_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verses');
    }
};
