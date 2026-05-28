<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Instructor Ahmed',
            'student_id' => 'DOC001',
            'teams_email' => 'DOC001@doc.jadara.edu.jo',
            'role' => 'instructor',
            'password' => bcrypt('123456'),
        ]);
        \App\Models\User::create([
            'name' => 'Instructor mohammad',
            'student_id' => 'DOC002',
            'teams_email' => 'DOC002@doc.jadara.edu.jo',
            'role' => 'instructor',
            'password' => bcrypt('123456'),
        ]);

        $students = [
            ['20221001', 'Ali Hassan'],
            ['20221002', 'Sara Mohamed'],
            ['20221003', 'Omar Khaled'],
            ['20221004', 'Nour Ahmed'],
            ['20221005', 'Fatima Ali'],
            ['20221006', 'Mohamed Saad'],
            ['20221007', 'Layla Hassan'],
            ['20221008', 'Youssef Omar'],
            ['20221009', 'Hana Khaled'],
            ['20221010', 'Salma Nabil'],
            ['20221011', 'Bilal Mahmoud'],
            ['20221012', 'Mona Samir'],
            ['20221013', 'Hassan Adel'],
            ['20221014', 'Yara Fadel'],
            ['20221015', 'Ahmad Saleh'],
            ['20221016', 'Rama Khalil'],
            ['20221017', 'Khaled Naser'],
            ['20221018', 'Dina Mahmoud'],
            ['20221019', 'Samer Ali'],
            ['20221020', 'Lina Yousef'],
            ['20221021', 'Amjad Hasan'],
            ['20221022', 'Razan Khaled'],
            ['20221023', 'Tariq Ahmad'],
            ['20221024', 'Mariam Ali'],
            ['20221025', 'Kareem Nabil'],
            ['20221026', 'Saja Omar'],
            ['20221027', 'Bashar Youssef'],
            ['20221028', 'Dana Mahmoud'],
            ['20221029', 'Zaid Salem'],
            ['20221030', 'Ruba Khalil'],
        ];

        foreach ($students as $student) {

            \App\Models\User::create([
                'name' => $student[1],
                'student_id' => $student[0],
                'teams_email' => $student[0] . '@std.jadara.edu.jo',
                'role' => 'student',
                'password' => bcrypt('123456'),
            ]);
        }
    }
}
