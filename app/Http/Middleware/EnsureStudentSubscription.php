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
        $exam_id = $request->route('id');
        if (!$exam_id) {
            return response()->json(['message' => 'Exam ID is required.'], 400);
        }

        $exam = Exam::find($exam_id);
        if (!$exam) {
            return response()->json(['message' => 'Exam not found.'], 404);
        }

        $teacher_id = $exam->teacher_id;
        if(!$teacher_id){
            return response()->json([
                'message' => 'Teacher ID is required'
            ], 400);
        }

        $subscription = Schedule::where('student_id', $student_id)
            ->where('teacher_id', $teacher_id)
            ->where('status', 'approved')
            ->exists();

        if(!$subscription){
            return response()->json([
                'message' => 'You are not subscribed to this teacher'
            ], 403);
        }

        return $next($request);
    }
}
