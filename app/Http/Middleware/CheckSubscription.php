<?php

namespace App\Http\Middleware;

use App\Models\Course;
use App\Models\Schedule;
use App\Models\Video;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{

    public function handle(Request $request, Closure $next): Response
    {
        $student_id = auth('api')->user()->id;

        $video_id = $request->route('id');

        if (!$video_id) {
            return response()->json(['message' => 'Video ID is required.'], 400);
        }

        $video = Video::find($video_id);

        if (!$video) {
            return response()->json(['message' => 'Video not found.'], 404);
        }

        $course = Course::find($video->course_id);

        if (!$course) {
            return response()->json(['message' => 'Course not found.'], 404);
        }

        $teacher_id = $course->teacher_id;

        $subscription = Schedule::where('student_id', $student_id)
            ->where('teacher_id', $teacher_id)
            ->where('status', 'approved')
            ->exists();

        if (!$subscription) {
            return response()->json(['message' => 'You are not subscribed to this teacher.'], 403);
        }

        return $next($request);
    }

}