<?php

namespace App\Http\Controllers\V1;
use App\Http\Requests\V1\UpdateUserRequest;
use App\Models\Course;
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
    use PaginationListTrait;
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

    public function update(UpdateUserRequest $request, $id){
        try{
            $user = User::findOrFail($id);
            $user->update($request->all());
            return $this->apiresponse($user, 'user updated successfully', 200);
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
            return $this->apiresponse(null, 'user deleted successfully', 200);
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

    public function create_teacher($id)
    {
        try {
            $user = User::find($id);
            $user->update([
                'role'=>'2'
            ]);

            $teacher = TeacherProfile::create([
                'user_id'=>$id
            ]);
            return $this->apiResponse($user,'he is new a teacher',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function assign_link(Request $request, $user_id)
    {
        try {
            $request->validate([
                'link' => 'required',
            ]);

            $teacher = TeacherProfile::where('user_id', $user_id)->first();

            if (!$teacher) {
                return $this->apiResponse(null, 'Teacher not found', 404);
            }

            $teacher->update([
                'link' => $request->link,
            ]);

            return $this->apiResponse($teacher, 'Link assigned successfully', 200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null, 'Please try again', 500);
        }

    }

// GET DATA OF STUDENTS
    public function students(Request $request)
    {
        return $this->PaginationList(
            $request,
            User::class,
            'students',
            ['id', 'fullName', 'email', 'status', 'is_verified', 'created_at'],
            [],
            mapCallback: function ($item) {
                return [
                    'id' => $item->id,
                    'fullName' => $item->fullName,
                    'email' => $item->email,
                    'status' => $item->status,
                    'is_verified' => $item->is_verified,
                    'created_at' => $item->created_at,
                ];
            }
        );
    }

    // GET DATA OF TEACHERS
    public function teachers()
    {
        try {
            $teachers = User::where('role', "2")
                ->with('teacherinfo')
                ->withCount(['courses', 'subscribedStudents'])
                ->withAvg('feedbacks', 'rate')
                ->get();

            return $this->apiResponse($teachers, 'This is a list of teachers', 200);

        } catch (\Exception $e) {
            return $this->apiResponse(null, 'Something went wrong: ' . $e->getMessage(), 500);
        }
    }

    public function applyConditions($query, $type, $request)
    {
        if ($type == 'students') {
            $query->where('role', '0');
        }

        if ($type == 'teachers') {
            $query->where('role', '2');
        }
        // FILTER BY STATUS
        if ($request->has('status') && in_array($request->input('status'), ['active', 'inactive'])) {
            $query->where('status', $request->input('status'));
        }

        // FILTER BY ISVERIFIED
        if ($request->has('is_verified')) {
            $query->where('is_verified', filter_var($request->input('is_verified'), FILTER_VALIDATE_BOOLEAN));
        }

        return $query;
    }

    public function latestStudentsAndCourses()
    {
        try {
            $latestStudents = User::where('role', '0')
                ->latest()
                ->take(2)
                ->get();

            $latestCourses = Course::latest()
                ->take(2)
                ->get();

            return $this->apiResponse([
                'latest_students' => $latestStudents,
                'latest_courses' => $latestCourses,
            ], 'Latest 2 registered students and courses', 200);

        } catch (\Exception $e) {
            return $this->apiResponse(null, 'Something went wrong: ' . $e->getMessage(), 500);
        }
    }


}
