<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class SectionController extends Controller
{
    public function store(Request $request, $courseId)
    {
        $request->validate([
            'name' => 'required|string',
            'formation_method' => 'required|in:manual,student_choice,random',
            'group_size' => 'required|integer|min:2|max:10',
        ]);

        $course = Course::findOrFail($courseId);

        if ($course->user_id !== auth()->id()) {
            return redirect('/instructor')->with('error', 'Unauthorized');
        }

        Section::create([
            'course_id' => $courseId,
            'name' => $request->name,
            'formation_method' => $request->formation_method,
            'group_size' => $request->group_size,
        ]);

        return redirect('/instructor/courses/' . $course->id)
        ->with('success', 'Section added successfully.');
    }

    public function downloadSample()
    {

        $fileName = 'students_sample.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['student_id', 'name'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['202310123', 'Ahmad Ali']);
            fputcsv($file, ['202310456', 'Sami Omar']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function uploadStudents(Request $request, $sectionId)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $section = Section::findOrFail($sectionId);

        if ($section->course->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        $data = array_map('str_getcsv', file($request->file('csv_file')->getRealPath()));

        $added = 0;
        $skipped = 0;

        foreach ($data as $index => $row) {

            if ($index === 0) continue; // header
            if (empty($row[0])) continue;

            $studentId = trim($row[0]);

            $student = User::where('student_id', $studentId)
                ->where('role', 'student')
                ->first();

            if (!$student) {
                $skipped++;
                continue;
            }

            if ($section->users()->where('users.id', $student->id)->exists()) {
                $skipped++;
                continue;
            }

            $alreadyInAnotherSection = $student->sections()
                ->where('course_id', $section->course_id)
                ->exists();

            if ($alreadyInAnotherSection) {
                $skipped++;
                continue;
            }

            $section->users()->attach($student->id);

            $section->course->users()->syncWithoutDetaching([$student->id]);

            $added++;
        }

        return back()->with('success', "$added students added, $skipped skipped.");
    }

    public function addStudentById(Request $request, $sectionId)
    {
        $section = Section::findOrFail($sectionId);

        if ($section->course->user_id !== auth()->id()) {
            return redirect('/instructor')->with('error', 'Unauthorized');
        }

        $request->validate(['student_id' => 'required']);

        $student = User::where('student_id', $request->student_id)
                        ->where('role', 'student')
                        ->first();

        if (!$student) {
            return back()->with('error', 'Student ID not found.');
        }

        if ($section->users()->where('users.id', $student->id)->exists()) {
            return back()->with('error', 'Student is already in this section.');
        }
        $alreadyInCourse = $student->sections()
            ->whereHas('course', function ($q) use ($section) {
                $q->where('id', $section->course_id);
            })
            ->exists();

        if ($alreadyInCourse) {
            return back()->with('error', 'Student already enrolled in another section of this course.');
        }

                $section->users()->syncWithoutDetaching([$student->id]);
        $section->course->users()->syncWithoutDetaching([$student->id]);

        return back()->with('success', "Student \"{$student->name}\" added to section.");
    }

    public function storeGroup(Request $request, $sectionId)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $section = Section::findOrFail($sectionId);

        if ($section->course->user_id !== auth()->id()) {
            return redirect('/instructor')->with('error', 'Unauthorized');
        }

        $section->groups()->create([
            'name' => $request->name,
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Group created successfully');
    }

    public function generateGroups($sectionId)
    {
        $section = Section::with('groups', 'users')->findOrFail($sectionId);

        if ($section->course->user_id !== auth()->id()) {
            return redirect('/instructor')->with('error', 'Unauthorized');
        }

        if ($section->formation_method !== 'random') {
            return redirect()->back()->with('error', 'Group formation method is not random');
        }

        if ($section->groups()->exists()) {
            return redirect()->back()->with('error', 'Groups are already generated for this section. Use the manual add/remove to make changes.');
        }

        if ($section->users->isEmpty()) {
            return redirect()->back()->with('error', 'No students enrolled in this section yet.');
        }

        $students = $section->users->shuffle();
        $groupSize = max(2, $section->group_size);
        $groups = [];
        $current = 0;
        $totalStudents = $students->count();

        while ($current < $totalStudents) {
            $groups[] = $students->slice($current, $groupSize);
            $current += $groupSize;
        }

        if (count($groups) > 1 && $groups[count($groups) - 1]->count() < 2) {
            $lastGroup = $groups[count($groups) - 1];
            unset($groups[count($groups) - 1]);
            $groups = array_values($groups);
            $groups[count($groups) - 1] = $groups[count($groups) - 1]->merge($lastGroup);
        }

        foreach ($groups as $index => $groupStudents) {
            $group = $section->groups()->create([
                'name' => 'Group ' . ($index + 1),
                'created_by' => auth()->id(),
                'is_random'  => true,
            ]);
            $group->members()->attach($groupStudents->pluck('id'));
        }

        $section->update(['random_locked' => true]);

        return redirect()->back()->with('success', count($groups) . ' groups generated successfully');
    }
}
