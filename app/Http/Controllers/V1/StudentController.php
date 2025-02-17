<?php

namespace App\Http\Controllers\V1;


use Exception;
use App\Models\Plan;
use App\Models\TeacherProfile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TeacherResource;
use App\Http\Requests\V1\StorePlanRequest;
use Illuminate\Support\Facades\Log;
class StudentController extends Controller
{
    use ApiResopnseTrait;
     public function __construct()
         {
             $this->middleware('auth:api');
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


}
