<?php

namespace App\Http\Controllers\V1;

use Exception;
use App\Models\User;
use App\Models\TeacherProfile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTeacherRequest;

class TeacherController extends Controller
{
    use ApiResopnseTrait;
    // public function __construct()
    // {
    //     $this->middleware('is_teacher');
    // }

    public function store(StoreTeacherRequest $request)
    {
        try {
                $userId = Auth::id();
                if ($userId == null) {
                    return response()->json([
                        'message' => 'User not authenticated',
                    ], 401);
                }

                if (TeacherProfile::where('user_id', $userId)->exists()) {
                    return response()->json([
                        'message' => 'Teacher profile already exists for this user',
                    ], 400);
                }


                $profilePicPath = null;
                if ($request->hasFile('profile_pic')) {
                    $profile_pictur = $request->file('profile_pic');
                    $profilePicPath = $profile_pictur->store('profile_pictures', 'public');
                }

                $teachers = TeacherProfile::create([
                'user_id'=>$userId,
                'fname' => $request->fname,
                'lname' => $request->lname,
                'bio' => $request->bio,
                'profile_pic' => $profilePicPath,
                'specialty' => $request->specialty,
                'years_of_experience' => $request->years_of_experience,
            ]);
            return response()->json([
                'message' => 'Teacher profile created successfully!',
                'teachers' => $teachers
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create teacher profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

        
        
}