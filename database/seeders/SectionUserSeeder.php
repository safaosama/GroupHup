<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sectionA = \App\Models\Section::find(1);
        $sectionB = \App\Models\Section::find(2);

        $studentsA = \App\Models\User::whereIn('student_id', [
            '20221001', '20221002', '20221003', '20221004',
            '20221005', '20221006', '20221007'
        ])->get();

        $studentsB = \App\Models\User::whereIn('student_id', [
            '20221008', '20221009', '20221010', '20221011',
            '20221012', '20221013', '20221014'
        ])->get();

        $sectionA->users()->attach($studentsA->pluck('id'));
        $sectionB->users()->attach($studentsB->pluck('id'));
    }
}
