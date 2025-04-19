<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{

    public function run(): void
    {
        $teachers = User::where('role', 2)->get();

        foreach ($teachers as $teacher) {
            Course::create([
                'teacher_id' => $teacher->id,
                'title' => 'Course by ' . $teacher->fullName,
                'description' => 'This is a course about Laravel basics.',
                'status' => 'published',
            ]);
        }
    }
}
