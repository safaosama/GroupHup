<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Course::create([
            'name' => 'Data Structures',
            'min_students' => 10,
            'max_students' => 50,
            'user_id' => 1, // Instructor Ahmed
        ]);
    }
}
