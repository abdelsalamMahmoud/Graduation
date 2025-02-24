<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
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
        }

        return $this->apiResponse($sessions,'Sessions generated successfully',200);
    }


}
