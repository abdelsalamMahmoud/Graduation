<?php

namespace Database\Seeders;

use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacheProfileSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = User::where('role', 2)->get();

        foreach ($teachers as $teacher) {
            TeacherProfile::create([
                'user_id' => $teacher->id,
                'fname' => 'Fname_' . $teacher->id,
                'lname' => 'Lname_' . $teacher->id,
                'bio' => 'Experienced teacher in various subjects.',
                'phone' => '0123456789',
                'profile_pic' => null,
                'specialty' => 'Science',
                'years_of_experience' => '5',
                'link' => 'https://example.com/teacher/' . $teacher->id,
            ]);
        }
    }
}
