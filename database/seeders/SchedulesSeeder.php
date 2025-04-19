<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchedulesSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::where('role', 0)->get();
        $teachers = User::where('role', 2)->get();

        foreach ($students as $student) {
            $teacher = $teachers->random();

            Schedule::create([
                'student_id' => $student->id,
                'teacher_id' => $teacher->id,
                'days' => json_encode(['Monday', 'Wednesday']),
                'time' => '14:00:00',
                'duration' => 60,
                'start_date' => now()->addDays(1),
                'end_date' => now()->addMonths(1),
                'status' => 'approved',
            ]);
        }
    }
}
