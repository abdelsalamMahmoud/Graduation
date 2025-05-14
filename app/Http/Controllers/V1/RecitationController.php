<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckVerseRequest;
use App\Models\Verse;

class RecitationController extends Controller
{
    use ApiResopnseTrait;

    public function checkVerse(CheckVerseRequest $request)
    {
        try {
            // Save audio temporarily
//            $audioPath = $request->file('audio')->store('recitations');

            // Get correct verse
            $verse = Verse::where('surah_id', $request->surah_id)
                ->where('verse_number', $request->verse_number)
                ->firstOrFail();

            // ---- Dummy ML response ----
            // Replace this with actual ML model integration
            $modelTranscription = "ٱلۡحَمۡدُ لِلَّهِ رَبِّ ٱلۡعَٰلَمِينَ";

            // Compare words
            $actualWords = explode(' ', strip_tags($verse->text));
            $predictedWords = explode(' ', $modelTranscription);

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

            return $this->apiResponse($data,'this is the result',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

}
