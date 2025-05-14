<?php

namespace Database\Seeders;

use App\Models\Surah;
use App\Models\Verse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class QuranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('data/quran.json'));
        $surahs = json_decode($json, true);

        foreach ($surahs as $surah) {
            // Insert Surah
            $surahModel = Surah::create([
                'id' => $surah['id'],
                'name' => $surah['name'],
                'transliteration' => $surah['transliteration'],
                'type' => $surah['type'],
                'verse_count' => $surah['total_verses'],
            ]);

            // Insert Verses
            foreach ($surah['verses'] as $verse) {
                Verse::create([
                    'surah_id' => $surahModel->id,
                    'verse_number' => $verse['id'],
                    'text' => $verse['text']
                ]);
            }
        }
    }
}
