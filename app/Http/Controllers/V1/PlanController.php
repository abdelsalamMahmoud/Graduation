<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StorePlanRequest;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    use ApiResopnseTrait;
    public function index()
    {
        try {
            $plans = Plan::where('user_id',auth('api')->user()->id)->paginate(10);
            return $this->apiResponse($plans,'these are your plans',200);

        } catch (\Exception $e) {
            return $this->apiResponse(null,'Please Try Again',400);
        }
    }


    public function store(StorePlanRequest $request)
    {
        try {
            $taskPerDay = $request->amount / $request->number_of_days ;
            $plan = Plan::create(array_merge(
                $request->except(['_token']),
                [
                    'task_per_day'=>$taskPerDay,
                    'user_id'=>auth('api')->user()->id
                ]
            ));
            return $this->apiResponse($plan,'your plan is Ready',201);

        } catch (\Exception $e) {
            return $this->apiResponse(null,'Please Try Again',400);
        }
    }

    public function updateProgress(Request $request, $id)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'progress' => 'required|numeric|min:0|max:100',
        ]);

        $progress = Plan::find($id);

        if (!$progress) {
            return response()->json([
                'message' => 'No progress record found for this student.'
            ], 404);
        }

        if ($progress->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized: You can only update your own progress'
            ], 403);
        }

        $progress->update([
            'progress' => $request->progress,
        ]);

        return response()->json([
            'message' => 'Progress updated successfully',
            'percentage_of_progress' => $progress->progress
        ], 200);
    }

    //

    public function ViewStudentProgress($studentId)
    {
        $user = auth('api')->user();

        if (!$user || $user->role !== '2') {
            return response()->json([
                'error' => 'Unauthorized'
            ], 403);
        }

        $progress = Plan::where('user_id', $studentId)->get();

        if ($progress->isEmpty()) {
            return response()->json([
                'message' => 'No progress found for this student'
            ], 404);
        }

        return response()->json([
            'student_id' => $studentId,
            'progress' => $progress
        ], 200);
    }

}
