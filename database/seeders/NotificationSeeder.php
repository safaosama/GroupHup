<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $instructors = User::where('role', 'instructor')->get();

        foreach ($students as $student) {
            Notification::create([
                'user_id' => $student->id,
                'title' => 'Welcome to GroupHup!',
                'message' => 'Welcome to the Group Management System. You can now join groups and collaborate with your classmates.',
            ]);
        }

        foreach ($instructors as $instructor) {
            Notification::create([
                'user_id' => $instructor->id,
                'title' => 'Welcome to GroupHup!',
                'message' => 'Welcome to the Group Management System. Start creating courses and managing student groups.',
            ]);
        }
    }
}
