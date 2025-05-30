<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckVerseRequest;
use App\Models\Verse;
use Illuminate\Support\Facades\Http;

class RecitationController extends Controller
{
    use ApiResopnseTrait;

    public function checkVerse(CheckVerseRequest $request)
    {
        try {
            // Get uploaded audio file and store it temporarily
            $file = $request->file('audio');
            $storedPath = $file->store('recitations');
            $fullPath = storage_path("app/{$storedPath}");

            // Get the correct verse from database
            $verse = Verse::where('surah_id', $request->surah_id)
                ->where('verse_number', $request->verse_number)
                ->firstOrFail();

            // Send audio to the ML model (use 'file' as field name)
            $response = Http::attach(
                'file',
                file_get_contents($fullPath),
                $file->getClientOriginalName()
            )->post('http://localhost:5000/transcribe/');

            if (!$response->ok()) {
                return response()->json(['error' => 'ML API failed'], 500);
            }

            $modelTranscription = $response->json()['transcription'] ?? '';

            // Compare exact words
            $actualWords = explode(' ', trim($verse->text));
            $predictedWords = explode(' ', trim($modelTranscription));

            $comparison = [];
            foreach ($actualWords as $i => $word) {
                $comparison[] = [
                    'word' => $word,
                    'correct' => isset($predictedWords[$i]) && $predictedWords[$i] === $word
                ];
            }

            $data = [
                'model_transcription' => $modelTranscription,
                'actual_text' => $verse->text,
                'word_match' => $comparison
            ];

            return $this->apiResponse($data, 'Comparison complete', 200);

        } catch (\Exception $exception) {
            return $this->apiResponse(null, 'An error occurred: ' . $exception->getMessage(), 500);
        }
    }

}
