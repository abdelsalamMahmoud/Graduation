<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Resources\TeacherProfileResource;
use App\Models\TeacherProfile;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    use ApiResopnseTrait;
    // public function __construct()
    // {
    //     $this->middleware('is_teacher');
    // }

    public function store(StoreTeacherRequest $request){
        try{
                $userId = Auth::id();
               if($userId==null){
                return response()->json([
                    'message'=>'User Not Authenticated'
                ], 401);
            }
            if (TeacherProfile::where('user_id', $userId)->exists()) {
                return response()->json([
                    'message' => 'Teacher Profile Already Exists'
                ], 400);
            }
            $teachers = TeacherProfile::create([
                'user_id'=>$userId,
                'fname'=>$request->fname,
                'lname'=>$request->lname,
                'bio'=>$request->bio,
                'phone'=>$request->phone,
                'profile_pic'=>$request->profile_pic,
                'specialty'=>$request->specialty,
                'years_of_experience'=>$request->years_of_experience,
            ]);
            return response()->json([
                'message'=>'Data Created Successfully',
                'teachers'=>$teachers,
            ],201);
        }
        catch(Exception $e){
            return response()->json([
                'message'=>'Failed To Store',
                'errors'=>$e->getMessage(),
            ],500);

        }
    }
    
}