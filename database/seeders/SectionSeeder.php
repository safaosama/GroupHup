<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Section::create([
            'course_id' => 1, // Data Structures
            'name' => 'Section A',
            'formation_method' => 'random',
            'group_size' => 4,
        ]);

        \App\Models\Section::create([
            'course_id' => 1,
            'name' => 'Section B',
            'formation_method' => 'student_choice',
            'group_size' => 3,
        ]);
    }
}
