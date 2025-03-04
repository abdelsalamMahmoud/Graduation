<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreCourseRequest;
use App\Http\Requests\V1\UpdateCourseRequest;
use App\Models\Course;

class CourseController extends Controller
{
    use ApiResopnseTrait;

    public function index(?string $id = null)
    {
        try {
            $user = auth('api')->user();
            $teacher_id = $user->role == 2 ? $user->id : ($id ?? null);
            if (!$teacher_id) {
                return $this->apiResponse(null, 'Invalid request', 400);
            }
            $courses = Course::where('teacher_id', $teacher_id)->paginate(10);
            return $this->apiResponse($courses, 'These are your courses', 200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null, 'Please try again', 500);
        }
    }

    public function store(StoreCourseRequest $request)
    {
        try {
            $course = Course::create(array_merge(
                $request->except(['_token']),
                [
                    'teacher_id'=>auth('api')->user()->id,
                ]
            ));
            return $this->apiResponse($course,'Course Created successfully',200);

        } catch (\Exception $e) {
            return $this->apiResponse(null,'Please Try Again',400);
        }
    }

    public function show($id)
    {
        try {
            $course = Course::find($id);
            return $this->apiResponse($course,'this is the course',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function update(UpdateCourseRequest $request, $id)
    {
        try {
            $course = Course::find($id);

            if($course->teacher_id != auth('api')->user()->id)
            {
                return $this->apiResponse(null,'you do not have the permission to update this course',200);
            }

            $data = $request->all();
            $course->update($data);
            return $this->apiResponse($course,'course updated successfully',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function destroy($id)
    {
        try {
            $course = Course::find($id);

            if($course->teacher_id != auth('api')->user()->id)
            {
                return $this->apiResponse(null,'you do not have the permission to delete this course',200);
            }

            $course->delete();
            return $this->apiResponse(null,'course deleted successfully',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function publish_course($id)
    {
        try {
            $course = Course::find($id);

            if($course->teacher_id != auth('api')->user()->id)
            {
                return $this->apiResponse(null,'you do not have the permission to publish this course',200);
            }
            $course->update([
                'status'=>'published'
            ]);
            return $this->apiResponse($course,'course published successfully',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }
}
