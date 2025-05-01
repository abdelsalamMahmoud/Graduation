<?php

namespace App\Http\Controllers\V1;
use Exception;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Requests\V1\StoreUserRequest;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Rate;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminController extends Controller
{
    use ApiResopnseTrait;
    use PaginationListTrait;

// GET COURSES
    public function course(Request $request)
    {
        
        return $this->PaginationList(
            $request,
            Course::class,
            'courses',
            ['id', 'title', 'teacher_id','status'],
            [
                'teacher'=> function ($query) {
                    $query->select(['id', 'fullName']);
                },
            ],
            
            function ($item) {
                return [
                    'title'=>$item->title,
                    'teacher'=> $item->teacher ? $item->teacher->fullName : 'Unknoun',
                    'status'=> $item->status,
                ];
            }
        );
    }


// GET EXAMS
    public function exams(Request $request)
    {
        return $this->PaginationList(
            $request,
            Exam::class,
            'exams',
            ['id', 'title', 'teacher_id'],
            [
                'teacher' => function ($query) {
                    $query->select(['id', 'fullName']);
                },
            ],
            function ($item) {
                return [
                    'title' => $item->title,
                    'teacher' => $item->teacher ? $item->teacher->fullName : 'unknown',
                ];
            }
        );
    }

    // GET FEEDBACK
    public function feedbacks(Request $request)
    {
        return $this->PaginationList(
            $request,
            Rate::class,
            'feedbacks',
            ['id', 'user_id', 'teacher_id', 'feedback', 'created_at'],
            [
                'user'=>function($query) {
                    $query->select('id', 'fullName')->where('role', 1);
                },
                'teacher'=>function($query) {
                    $query->select('id', 'fullName')->where('role', '2');
                },
            ],
            function ($item) {
                return [
                    'id' => $item->id,
                    'student_nam'=>$item->user ? $item->user->fullName : 'unknoun',
                    'teacher_name'=>$item->teacher ? $item->teacher->fullName : 'Unknown',
                    'feedback'=>$item->feedback ?? 'Not Found feedback',
                    'created_at'=>$item->created_at ? $item->created_at : null,
                ];
            }
        );
  
    }


    protected function applyConditions($query, $type ,$request)
    {
        if ($type == 'courses') {
          return $query->has('teacher');
        }
        // if ($request->has('status') && in_array($request->input('status'), ['draft', 'published'])) {
        //     $query->where('status', $request->input('status'));
        // }
       
        if ($type == 'exams') {
            return $query->has('teacher');
        }
        elseif ($type == 'feedbacks') {
            if ($request->has('teacher_id')) {
                $query->where('teacher_id', $request->input('teacher_id'));
            }
            if ($request->has('student_id')) {
                $query->where('user_id', $request->input('student_id'));
            }
            
        return $query;
    }
    
    }


    public function insights(){
        try{

            $totalStudents = User::where('role', '0')->count();

            $totalTeachers = User::where('role', '2')->count();

            $totalCourses = Course::count();

                        return response()->json([
                            'data'=>[
                            'total_students'=> $totalStudents,
                            'total_teachers'=> $totalTeachers,
                            'total_courses'=>$totalCourses,
                            ],
                            'message'=> 'Insights retrieved successfully',
                        ], 200);
                      
        }
        catch (\Exception $exception) {
            return $this->apiResponse(null,'Error retrieving insights',500);
    }
    }

    }
