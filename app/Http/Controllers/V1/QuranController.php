<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Surah;
use App\Models\Verse;

class QuranController extends Controller
{
    use ApiResopnseTrait;
    public function getSurahs()
    {
        try {
            $surahs = Surah::select('id', 'name', 'transliteration', 'verse_count')->get();
            if($surahs->isEmpty())
            {
                return $this->apiResponse(null,'Surahs not found',404);
            }
            return $this->apiResponse($surahs,'these are all Surahs',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function getVerses($id)
    {
        try {
            $verses = Verse::where('surah_id', $id)
                ->select('verse_number', 'text')
                ->orderBy('verse_number')
                ->get();
            if($verses->isEmpty())
            {
                return $this->apiResponse(null,'Verses not found',404);
            }
            return $this->apiResponse($verses,'these are all Verses',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }
}
