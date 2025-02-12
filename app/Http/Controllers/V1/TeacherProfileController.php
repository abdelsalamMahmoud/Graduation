<?php

namespace App\Http\Controllers\V1;

use Exception;
use App\Models\User;
use App\Models\TeacherProfile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\V1\StoreTeacherRequest;
// use App\Http\Requests\StoreTeacherRequest;

class TeacherProfileController extends Controller
{
    use ApiResopnseTrait;

    public function update_info(StoreTeacherRequest $request)
    {
        try {
                $userId = auth('api')->user()->id;

                if (TeacherProfile::where('user_id', $userId)->exists()) {
                    return response()->json([
                        'message' => 'Teacher profile already exists for this user',
                    ], 400);
                }


                $profilePicPath = null;
                if ($request->hasFile('profile_pic')) {
                    $profile_picture = $request->file('profile_pic');
                    $profilePicPath = $profile_picture->store('profile_pictures', 'public');
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
