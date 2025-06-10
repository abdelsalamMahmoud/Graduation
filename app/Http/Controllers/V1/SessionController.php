<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinishSessionRequest;
use App\Http\Requests\UpdateSessionRequest;
use App\Models\Schedule;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    use ApiResopnseTrait;
    public function generateSessionsForSchedule($schedule_id)
    {
        $schedule = Schedule::find($schedule_id);

        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        $startDate = Carbon::parse($schedule->start_date);
        $endDate = Carbon::parse($schedule->end_date);
        $daysOfWeek = json_decode($schedule->days, true);

        if (!is_array($daysOfWeek)) {
            return response()->json(['message' => 'Invalid days format'], 400);
        }

        $sessions = [];

        while ($startDate->lte($endDate)) {
            if (in_array($startDate->format('l'), $daysOfWeek)) {
                $sessions[] = [
                    'schedule_id' => $schedule->id,
                    'teacher_id' => $schedule->teacher_id,
                    'student_id' => $schedule->student_id,
                    'date' => $startDate->format('Y-m-d'), // Store date separately
                    'time' => $schedule->time, // Store time separately
                    'session_time' => $startDate->format('Y-m-d') . ' ' . $schedule->time, // Full datetime
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $startDate->addDay();
        }

        if (!empty($sessions)) {
            Session::insert($sessions);
            $schedule->delete();
        }
        return $this->apiResponse($sessions,'Sessions generated successfully',200);
    }


    public function teacher_today_sessions()
    {
        try {
            $todaySessions = Session::where('teacher_id', auth('api')->user()->id)
                ->where('date', Carbon::today()->format('Y-m-d'))
                ->with([
                    'student:id,fullName',
                    'teacher.teacherinfo:user_id,link'
                ])
                ->paginate(10);

            return $this->apiResponse($todaySessions, "these are today's sessions", 200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null, 'please try again', 404);
        }
    }

    public function update(UpdateSessionRequest $request , $id)
    {

    }

    public function cancel_session($id)
    {
        try {
            $session = Session::find($id);

            if($session->teacher_id != auth('api')->user()->id)
            {
                return $this->apiResponse(null,'you do not have the permission to cancel this session',200);
            }

            $session->update([
                'status'=>'cancelled'
            ]);

            return $this->apiResponse($session,'session canceled successfully',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function delete($id)
    {
        try {
        $session = Session::find($id);

        if($session->teacher_id != auth('api')->user()->id)
        {
            return $this->apiResponse(null,'you do not have the permission to delete this session',200);
        }
        $session->delete();
        return $this->apiResponse(null,'session deleted successfully',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function finish_session(FinishSessionRequest $request , $id)
    {
        try {
            $session = Session::find($id);

            if($session->teacher_id != auth('api')->user()->id)
            {
                return $this->apiResponse(null,'you do not have the permission to finish this session',200);
            }

            $session->update(array_merge(
                $request->except(['_token']),
                [
                    'status'=>'completed',
                ]
            ));
            return $this->apiResponse($session,'session completed successfully',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }

    }


    public function student_today_sessions()
    {
        try {
            $todaySessions = Session::where('student_id',auth('api')->user()->id)->where('date', Carbon::today()->format('Y-m-d'))->with('teacher.teacherinfo')->paginate(10);
            return $this->apiResponse($todaySessions,"these are today's sessions",200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function student_all_sessions()
    {
        try {
            $todaySessions = Session::where('student_id',auth('api')->user()->id)->with('teacher.teacherinfo')->paginate(10);
            return $this->apiResponse($todaySessions,"these are all sessions for you",200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

}
