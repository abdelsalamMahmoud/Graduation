<?php

namespace App\Http\Controllers\V1;
use App\Models\TeacherProfile;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResopnseTrait;

    public function index(){
        try{
            $users = User::paginate(10);
            $userresource = UserResource::collection($users);
            return $this->apiResponse($userresource, 'ok', 200);
        }
        catch(\Exception $e){
            return response()->json([
                'message'=>'An error occurred while fetching users',
                'error'=>$e->getMessage(),
            ], 500);
        }
    }

    public function show($id){
        try{
            $Users = new UserResource(User::findOrFail($id));
            return $this->apiresponse($Users, 'ok', 200);
        }
        catch(ModelNotFoundException $e){
            return response()->json([
                'message' => 'Error fetching users',
                'error' => $e->getMessage(),
            ], 404);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function store(StoreUserRequest $request)
    {
        try {
            $user = User::create([
                'fullName' => $request->fullName,
                'email' => $request->email,
                'password' => Hash::make('12345678'),
                'role' => $request->role,
            ]);
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(StoreUserRequest $request, $id){
        try{
            $user = User::findOrFail($id);
            $user->update($request->all());
            return $this->apiresponse($user, 'this user is updated', 200);
        }
        catch(Exception $e){
            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage(),
            ], 500);

        }
    }

    public function destroy($id){
        try{
            $user = User::find($id);
            $user->delete();
            return $this->apiresponse(null, 'this user is deleted', 200);
        }
        catch (ModelNotFoundException $e) {
            return $this->apiResponse(null, 'User not found', 404);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete this user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteMultipleUsers(Request $request){

        $validator = Validator::make($request->all(), [
            'user_ids'   => 'required|array',
            'user_ids.*' => 'integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        User::whereIn('id', $request->user_ids)->delete();

        return response()->json([
            'message' => 'Selected users have been deleted successfully',
            'deleted_user_ids' => $request->user_ids
        ], 200);
    }

    public function make_teacher($id)
    {
        try {
            $user = User::find($id);
            $user->update([
                'role'=>'2'
            ]);

            $teacher = TeacherProfile::create([
                'user_id'=>$id
            ]);
            return $this->apiResponse($user,'he is now a teacher',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

//    public function assign_link(Request $request,$user_id)
//    {
//        try {
//            $teacher = TeacherProfile::where('user_id',$user_id)->first();
//            $teacher->update([
//                'link'=>$request->link,
//            ]);
//            return $this->apiResponse($teacher,'link assigned successfully',200);
//        } catch (\Exception $exception) {
//            return $this->apiResponse(null,'please try again',404);
//        }
//    }


}
