<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreRateRequest;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class RateController extends Controller
{
    //
    public function StoreRate(StoreRateRequest $request){
        try{
            
            $rate = Rate::create($request->all());
            return response()->json([
                'message'=>'Rating submitted successfully',
                'data'=>$rate,
            ],201);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create rate',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateRate($id, StoreRateRequest $request)
{
    try {
        $rate = Rate::findOrFail($id);

        $rate->update([
            'rate' => $request->rate,  
            'feedback' => $request->feedback,  
        ]);

        return response()->json([
            'message' => 'Rating updated successfully',
            'data' => $rate,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to update rate',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function getTeacherRatings($teacher_id)
    {
        $ratings = Rate::where('teacher_id', $teacher_id)->get();

        if ($ratings->isEmpty()) {
            return response()->json(['message' => 'No ratings found for this teacher'], 404);
        }

        return response()->json([
            'teacher_id' => $teacher_id,
            'ratings' => $ratings
        ], 200);
    }

    public function averageRating($teacher_id)
    {
        $average = Rate::where('teacher_id', $teacher_id)->avg('rate');

        return response()->json([
            'teacher_id' => $teacher_id,
            'average_rating' => $average ? round($average, 2) : 0
        ], 200);
    }

    public function deleteRate($id){
        try{
            $rate = Rate::findOrFail($id);
            $rate->delete();

            return response()->json([
                'message' => 'Rating deleted successfully', 
            ],200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Rating Not Found',
            ], 404);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Faild to delet rating',
            ],500);
        }
    }
}