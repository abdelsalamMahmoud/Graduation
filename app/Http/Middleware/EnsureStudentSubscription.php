<?php

namespace App\Http\Middleware;

use App\Models\Exam;
use App\Models\Schedule;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $student_id = auth('api')->user()->id;
        $exam_id = $request->route('exam_id') ?? null;
        $teacher_id = $request->route('teacher_id');

        if(!$teacher_id){
            return response()->json([
                'message' => 'Teacher ID is required'
            ], 400);
        }

        if ($exam_id) {
            $exam = Exam::find($exam_id);

            if (!$exam) {
                return response()->json([
                    'message' => 'Exam not found'
                ], 404);
            }

            if ($exam->teacher_id != $teacher_id) {
                return response()->json([
                    'message' => 'This exam does not belong to the specified teacher'
                ], 403);
            }
        }

        $subscription = Schedule::where('student_id', $student_id)
            ->where('teacher_id', $teacher_id)
            ->where('status', 'approved')
            ->exists();

        if(!$subscription){
            return response()->json([
                'message' => 'You are not subscriped to this teacher'
            ], 403);
        }

        return $next($request);
    }
}