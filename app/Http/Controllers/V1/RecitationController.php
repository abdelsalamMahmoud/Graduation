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
            // 1. استلام الصوت وتخزينه مؤقتًا
            $file = $request->file('audio');
            $storedPath = $file->store('recitations');
            $fullPath = storage_path("app/{$storedPath}");

            // 2. جلب الآية من قاعدة البيانات
            $verse = Verse::where('surah_id', $request->surah_id)
                ->where('verse_number', $request->verse_number)
                ->firstOrFail();

            // 3. إرسال الصوت إلى موديل التحويل
            $response = Http::attach(
                'file',
                file_get_contents($fullPath),
                $file->getClientOriginalName()
            )->post('http://localhost:5000/transcribe/');

            if (!$response->ok()) {
                return response()->json(['error' => 'ML API failed'], 500);
            }

            $modelTranscription = $response->json()['transcription'] ?? '';

            // 4. تنظيف النصوص للمقارنة (بدون تشكيل)
            $cleanActual = $this->normalizeArabic($verse->text);
            $cleanPredicted = $this->normalizeArabic($modelTranscription);

            $actualWords = preg_split('/\s+/', $cleanActual);
            $predictedWords = preg_split('/\s+/', $cleanPredicted);

            // 5. مقارنة: لكل كلمة في الآية الأصلية، هل اتقالت مرة واحدة على الأقل؟
            $comparison = $this->compareWordOccurrences($actualWords, $predictedWords);

            // 6. إعداد الاستجابة مع النصوص الأصلية كما هي
            $data = [
                'actual_text' => $verse->text,
                'model_transcription' => $modelTranscription,
                'word_match' => $comparison
            ];

            return $this->apiResponse($data, 'Comparison complete', 200);

        } catch (\Exception $exception) {
            return $this->apiResponse(null, 'An error occurred: ' . $exception->getMessage(), 500);
        }
    }

    private function normalizeArabic($text)
    {
        // إزالة التشكيل
        $text = preg_replace('/[ًٌٍَُِّْـٰ]/u', '', $text);

        // إزالة الرموز القرآنية (مثل ۥ، ۩، ۞، ۝)
        $text = preg_replace('/[۞۩۝ۣۖۗۘۙۚۛۜ۟۠ۡۢۤۥۦ]/u', '', $text);

        // توحيد بعض الحروف
        $text = str_replace(['أ','إ','آ','ٱ'], 'ا', $text);

        return trim($text);
    }
    private function compareWordOccurrences(array $actualWords, array $predictedWords)
    {
        $comparison = [];
        $usedPredictedIndices = [];

        foreach ($actualWords as $i => $word) {
            $matchedIndex = null;

            foreach ($predictedWords as $j => $predictedWord) {
                if (in_array($j, $usedPredictedIndices)) continue;

                if ($predictedWord === $word) {
                    $matchedIndex = $j;
                    $usedPredictedIndices[] = $j;
                    break;
                }
            }

            $comparison[] = [
                'word' => $word,
                'correct' => $matchedIndex !== null
            ];
        }

        return $comparison;
    }

}
