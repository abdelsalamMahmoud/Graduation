<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Exception;
use App\Models\Plan;
use App\Models\TeacherProfile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TeacherResource;
use App\Http\Requests\V1\StorePlanRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Course;


class StudentController extends Controller

{
    use ApiResopnseTrait;
     public function __construct()
     {
         $this->middleware('auth:api');
     }

        public function get_notifications()
        {
            try {
                $user = User::find(auth('api')->user()->id);
                $notifications = $user->notifications;
                return $this->apiResponse($notifications,'these are your notifications',200);
            } catch (\Exception $exception) {
                return $this->apiResponse(null,'please try again',404);
            }
        }

        public function index()
        {
            try {
                $teachers = TeacherProfile::paginate(10);

                if ($teachers->isEmpty()) {
                    return response()->json([
                        'message' => 'No teachers found',
                    ], 404);
                }
                $teacherresource = TeacherResource::collection($teachers);
                return $this->apiResponse($teacherresource, 'ok', 200);

            } catch (Exception $e) {
                return response()->json([
                    'message' => 'An error while fetching teachers',
                    'errors' => $e->getMessage(),
                ], 500);
            }
        }

        public function latestCourses(Request $request)
        {
            try {
                $student = $request->user();

                $teacher = $student->teacher;

                $latestCourses = Course::where('teacher_id', $teacher->user_id)->latest()->take(2)->get();

                return response()->json([
                    'message' => 'Latest 2 courses retrieved successfully',
                    'data' => $latestCourses
                ], 200);

            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Error fetching courses',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }



        public function latestNotifications(Request $request)
        {
            try {
                $student = $request->user();
                $notifications = $student->notifications()->latest()->take(3)->get();

                return response()->json([
                    'message' => 'Latest 3 notifications retrieved successfully',
                    'data' => $notifications
                ], 200);

            } catch (Exception $e) {
                return response()->json([
                    'message' => 'An error occurred while fetching notifications',
                    'errors' => $e->getMessage(),
                ], 500);
            }
        }


}
