<?php

namespace App\Http\Controllers\V1;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class AdminController extends Controller
{
    use ApiResopnseTrait;
    // public function __construct()
    // {
    //     $this->middleware('is_admin');
    // }

    public function index(){
        $users = User::paginate(5);
        if($users->isEmpty()){
            return $this->apiResponse([], 'Not Found Users', 404);
        }
        $userresource = UserResource::collection($users);
        return $this->apiResponse($userresource, 'ok', 200);
    }

    public function show($id){
        $Users = new UserResource(User::find($id));
        if($Users){
            return $this->apiresponse($Users, 'ok', 200);
        }
        return $this->apiresponse(null, 'this User not found', 401);
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'fullName' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'role' => 'required|in:0,1,2',
        'status' => 'nullable|string',
        'verification_code' => 'nullable|string',
        'verification_expires_at' => 'nullable|date',
        'is_verified' => 'boolean',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 400);
    }

    $password = Hash::make($request->password);

    $user = User::create([
        'fullName' => $request->fullName,
        'email' => $request->email,
        'password' => $password, 
        'role' => $request->role,
        'status' => $request->status,
        'verification_code' => $request->verification_code,
        'verification_expires_at' => $request->verification_expires_at,
        'is_verified' => $request->is_verified,
    ]);

    if ($user) {
        return response()->json([
            'message' => 'User created successfully!',
            'user' => $user
        ], 201);
    } else {
        return response()->json([
            'message' => 'Failed to create user'
        ], 500);
    }
}

    public function update(Request $request, $id){
        $user = User::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'role' => 'required|in:0,1,2',
        'status' => 'nullable|string',
        'is_verified' => 'boolean',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::find($id);
        if(!$user){
            return $this->apiresponse(null, 'this user not found', 404);
        }
        $user->update($request->all());
        if($user){
            return $this->apiresponse($user, 'this user is updated', 201);
        }
        return $this->apiresponse(null, 'Failed to update this user', 500);
    }

    public function destroy($id){
        $user = User::find($id);
        if(!$user){
            return $this->apiresponse(null, 'this user not found', 404);
        }
        $user->delete();
        return $this->apiresponse(null, 'this user deleted', 200);
    }
}