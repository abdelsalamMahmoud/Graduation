<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreScheduleRequest;
use App\Http\Requests\V1\updateScheduleRequest;
use App\Models\Schedule;
use App\Models\User;
use App\Notifications\SchedulesNotification;

class ScheduleController extends Controller
{
    use ApiResopnseTrait;

    public function index()
    {
        try {
            $schedule_requests = Schedule::where('teacher_id', auth('api')->user()->id)->where('status','pending')->with('student:id,fullName')->paginate(10);
            return $this->apiResponse($schedule_requests, 'These are all requests', 200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null, 'Please try again', 500);
        }
    }

    public function store(StoreScheduleRequest $request)
    {
        try {
            $schedule = Schedule::create(array_merge(
                $request->except(['_token']),
                [
                    'student_id'=>auth('api')->user()->id,
                    'days'=>json_encode($request->days)
                ]
            ));
            return $this->apiResponse($schedule,'schedule requested successfully and waiting for approval',200);

        } catch (\Exception $e) {
            return $this->apiResponse(null,'Please Try Again',400);
        }
    }

    public function accept($id)
    {
        try {
            $schedule = Schedule::find($id);
            $student = User::find($schedule->student_id);
            $teacher = User::with('teacherinfo')->find($schedule->teacher_id);
            $schedule->update([
                'status'=>'approved',
            ]);

            $student->notify(new SchedulesNotification($teacher->teacherinfo->fname,' تم قبوله بواسطة المعلم'));

            return $this->apiResponse($schedule,'schedule accepted successfully',201);

        } catch (\Exception $e) {
            return $this->apiResponse(null,'Please Try Again',400);
        }
    }

    public function reject($id)
    {
        try {
            $schedule = Schedule::find($id);
            $student = User::find($schedule->student_id);
            $teacher = User::with('teacherinfo')->find($schedule->teacher_id);
            $schedule->update([
                'status'=>'rejected',
            ]);
            $student->notify(new SchedulesNotification($teacher->teacherinfo->fname,' تم رفضه بواسطة المعلم'));

            return $this->apiResponse($schedule,'schedule rejected successfully',201);

        } catch (\Exception $e) {
            return $this->apiResponse(null,'Please Try Again',400);
        }
    }

    public function update(updateScheduleRequest $request , $id)
    {


    }

    public function destroy($id)
    {
        try {
            $schedule = Schedule::find($id);
            $schedule->delete();
            return $this->apiResponse(null,'schedule deleted successfully',201);

        } catch (\Exception $e) {
            return $this->apiResponse(null,'Please Try Again',400);
        }
    }

    public function get_accepted_schedules()
    {
        try {
            $schedules = Schedule::where('teacher_id',auth('api')->user()->id)->where('status','approved')->with('student')->paginate(10);
            return $this->apiResponse($schedules,'these are accepted schedules',200);
        } catch (\Exception $e) {
            return $this->apiResponse(null,'Please Try Again',400);
        }
    }


    public function schedules_requests_list()
    {
        try {
            $schedules_requests = Schedule::where('student_id', auth('api')->user()->id)->with('teacher.teacherinfo')->paginate(10);
            return $this->apiResponse($schedules_requests, 'These are your requests', 200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null, 'Please try again', 500);
        }
    }

    public function cancel_schedule_request($id)
    {
        try {
            $schedule = Schedule::find($id);
            if (!$schedule)
            {
                return $this->apiResponse(null,'schedule not found',404);
            }
            if($schedule->student_id != auth('api')->user()->id || $schedule->status == 'approved')
            {
                return $this->apiResponse(null,'you can not cancel this schedule',200);
            }
            $schedule->delete();
            return $this->apiResponse(null,'schedule canceled successfully',201);

        } catch (\Exception $e) {
            return $this->apiResponse(null,'Please Try Again',400);
        }
    }


}
