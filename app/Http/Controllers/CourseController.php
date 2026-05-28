<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'min_students' => 'required|integer|min:1',
            'max_students' => 'required|integer|gte:min_students',
        ]);

        Course::create([
            'name' => $request->name,
            'min_students' => $request->min_students,
            'max_students' => $request->max_students,
            'user_id' => auth()->id(),
        ]);

        // Notification for instructor
        \App\Models\Notification::create([
            'user_id' => auth()->id(),
            'title' => 'Course Created Successfully',
            'message' => "You have successfully created the course '{$request->name}'.",
            'type' => 'course',
        ]);

        return redirect('/instructor/courses/')->with('success', 'Course created successfully!');
    }

    public function join($id)
    {
        $user = auth()->user();

        // Check if already enrolled
        if ($user->courses()->where('course_id', $id)->exists()) {
            return back()->with('error', 'You already joined this course.');
        }

        // Check if student already has a group in this course
        $alreadyInGroup = $user->groups()
            ->whereHas('course', function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->exists();

        if ($alreadyInGroup) {
            return back()->with('error', 'You are already in a group in this course.');
        }

        $user->courses()->attach($id);

        return back()->with('success', 'Joined successfully!');
    }

    public function leaveGroup($groupId)
    {
        $group = Group::findOrFail($groupId);

        if ($group->is_random) {
            return back()->with('error', 'You cannot leave a random group.');
        }

        $user = auth()->user();
        $group->members()->detach($user->id);
        return back()->with('success', 'You left the group successfully.');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        if ($course->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        $course->delete();

        return back()->with('success', 'Course deleted successfully!');
    }

    // instructor adds student manually
    public function addStudent(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        if ($course->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        $request->validate([
            'student_id' => 'required',
        ]);

        $student = \App\Models\User::where('student_id', $request->student_id)
            ->where('role', 'student')
            ->first();

        if (!$student) {
            return back()->with('error', 'Student ID not found.');
        }

        if ($course->users()->where('users.id', $student->id)->exists()) {
            return back()->with('error', 'Student already enrolled.');
        }

        $course->users()->attach($student->id);

        return back()->with('success', "Student '{$student->name}' added successfully.");
    }

    // Remove student
    public function removeStudent($courseId, $studentId)
    {
        $course = Course::findOrFail($courseId);

        if ($course->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        // Remove student from course
        $course->users()->detach($studentId);

        // Remove student from sections
        $course->sections()->each(function ($section) use ($studentId) {
            $section->users()->detach($studentId);
        });

        // Remove student from groups
        foreach ($course->groups as $group) {
            $group->members()->detach($studentId);
        }

        return back()->with('success', 'Student removed successfully.');
    }

}
