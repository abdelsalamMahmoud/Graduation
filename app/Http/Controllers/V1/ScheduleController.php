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
            $teacher = User::find($schedule->teacher_id);
            $schedule->update([
                'status'=>'approved',
            ]);

            $student->notify(new SchedulesNotification($teacher->fullName,' Approved by teacher'));

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
            $teacher = User::find($schedule->teacher_id);
            $schedule->update([
                'status'=>'rejected',
            ]);
            $student->notify(new SchedulesNotification($teacher->fullName,' Rejected by teacher'));

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


}
