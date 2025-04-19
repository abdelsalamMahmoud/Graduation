<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{

    public function run(): void
    {
        // Create students
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'fullName' => "Student $i",
                'email' => "student$i@example.com",
                'password' => Hash::make('password'), // default password
                'role' => '0', // student
                'is_verified' => true,
            ]);
        }

        // Create teachers
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'fullName' => "Teacher $i",
                'email' => "teacher$i@example.com",
                'password' => Hash::make('password'),
                'role' => '2', // teacher
                'is_verified' => true,
            ]);
        }
    }
}
