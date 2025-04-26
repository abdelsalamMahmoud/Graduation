<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreCourseRequest;
use App\Http\Requests\V1\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Schedule;

class CourseController extends Controller
{
    use ApiResopnseTrait;

    public function index()
    {
        try {
            $user = auth('api')->user();

            if ($user->role == 2) {
                $teacherIds = [$user->id];
            } elseif ($user->role == 0) {

                $teacherIds = Schedule::where('student_id', $user->id)
                    ->where('status','approved')
                    ->pluck('teacher_id')
                    ->unique()
                    ->toArray();

                if (empty($teacherIds)) {
                    return $this->apiResponse(null, 'No assigned teachers found', 400);
                }
            } else {
                $teacherIds = [1];
            }

            $courses = Course::whereIn('teacher_id', $teacherIds)->with('teacher.teacherinfo')->paginate(10);
            return $this->apiResponse($courses, 'These are your courses', 200);

        } catch (\Exception $exception) {
            return $this->apiResponse(null, 'Please try again', 500);
        }
    }

    public function store(StoreCourseRequest $request)
    {
        try {
            $data = $request->except(['_token']);
            $data['teacher_id'] = auth('api')->user()->id;

            if ($request->hasFile('cover_image')) {
                $image = $request->file('cover_image');
                $imagePath = $image->store('courses', 'public'); // stores in storage/app/public/courses
                $data['cover_image'] = $imagePath;
            }

            $course = Course::create($data);

            return $this->apiResponse($course, 'Course Created successfully', 200);

        } catch (\Exception $e) {
            return $this->apiResponse(null, 'Please Try Again', 400);
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
