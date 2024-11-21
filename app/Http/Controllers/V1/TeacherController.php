<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Models\TeacherProfile;
use App\Http\Controllers\Controller;
use App\Http\Resources\TeacherProfileResource;

class TeacherController extends Controller
{
    use ApiResopnseTrait;
    public function __construct()
    {
        $this->middleware('is_teacher');
    }
    
    public function index(){
        $teachers = User::where('role',2)->paginate(5);
        $teacherresource=TeacherProfileResource::collection($teachers);
        return $this->apiResponse($teacherresource, 'ok', 200);
    }
    
}