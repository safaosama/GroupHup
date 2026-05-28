<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Course;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function store(Request $request, $courseId)
    {
        return back()->with('error', 'Group creation must be done from a section detail page.');
    }

    public function studentCreate(Request $request)
    {
        $request->validate(['section_id' => 'required|exists:sections,id']);

        $section = Section::with('course')->findOrFail($request->section_id);
        $user    = auth()->user();

        if (!$section->users()->where('users.id', $user->id)->exists()) {
            return back()->with('error', 'You are not enrolled in this section.');
        }

        if ($section->formation_method !== 'student_choice') {
            return back()->with('error', 'Group creation is not allowed for this section.');
        }

        $alreadyInGroup = $user->groups()
            ->where('section_id', $section->id)
            ->exists();

        if ($alreadyInGroup) {
            return back()->with('error', 'You already have a group in this section.');
        }

        $request->validate(['name' => 'required|string|max:100']);

        $group = $section->groups()->create([
            'name'       => $request->name,
            'created_by' => $user->id,
            'is_random'  => false,
        ]);

        $group->members()->attach($user->id);


        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title'   => 'Group Created',
            'message' => "You created group \"{$group->name}\" in {$section->course->name}.",
            'type'    => 'group',
        ]);

        return back()->with('success', "Group \"{$group->name}\" created successfully!");
    }

    public function join($groupId)
    {
        $group   = Group::with('section.course')->findOrFail($groupId);
        $section = $group->section;
        $user    = auth()->user();

        if ($section->formation_method !== 'student_choice') {
            return back()->with('error', 'You cannot join groups in this section.');
        }

        if (!$section->users()->where('users.id', $user->id)->exists()) {
            return back()->with('error', 'You are not enrolled in this section.');
        }

        $alreadyInGroup = $user->groups()
            ->where('section_id', $section->id)
            ->exists();

        if ($alreadyInGroup) {
            return back()->with('error', 'You are already in a group in this section.');
        }

        if ($group->members()->count() >= $section->group_size) {
        return back()->with('error', 'This group is full.');
        }

        $group->members()->attach($user->id);

        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title'   => 'Joined a Group',
            'message' => "You joined group \"{$group->name}\" in {$course->name}.",
            'type'    => 'group',
        ]);

        return back()->with('success', 'Joined group successfully!');
    }

    public function leave($groupId)
    {
        $group = Group::with('section')->findOrFail($groupId);

        if ($group->section->formation_method === 'random') {
            return back()->with('error', 'You cannot leave a randomly assigned group.');
        }

        if ($group->section->formation_method === 'manual') {
            return back()->with('error', 'You cannot leave a manually assigned group. Contact your instructor.');
        }

        $group->members()->detach(auth()->id());

        return back()->with('success', 'Left group successfully.');
    }

    public function show($groupId)
    {
        $group = Group::with(['members', 'section.course', 'creator'])->findOrFail($groupId);

        if ($group->section->course->user_id !== auth()->id()) {
            return redirect('/instructor');
        }

        return view('instructor.group-details', compact('group'));
    }

    public function destroy($id)
    {
        $group = Group::findOrFail($id);

        if ($group->section->course->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        $group->members()->detach();
        $group->delete();

        return back()->with('success', 'Group deleted successfully!');
    }

    public function addMember(Request $request, $groupId)
    {
        $group = Group::with('section.course')->findOrFail($groupId);

        if ($group->section->course->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        $request->validate(['student_id' => 'required']);

        $student = User::where('student_id', $request->student_id)
                        ->where('role', 'student')
                        ->first();

        if (!$student) {
            return back()->with('error', 'Student ID not found.');
        }

        if (!$group->section->users()->where('users.id', $student->id)->exists()) {
            return back()->with('error', 'Student is not enrolled in this section.');
        }

        $alreadyInGroupInSection = $student->groups()
            ->where('section_id', $group->section_id)
            ->exists();

        if ($alreadyInGroupInSection) {
            return back()->with('error', 'Student is already in a group in this section.');
        }

        if ($group->members()->where('users.id', $student->id)->exists()) {
            return back()->with('error', 'Student is already in this group.');
        }

        $group->members()->attach($student->id);

        \App\Models\Notification::create([
            'user_id' => $student->id,
            'title'   => 'Added to a Group',
            'message' => "You were added to group \"{$group->name}\" by your instructor.",
            'type'    => 'group',
        ]);

        return back()->with('success', "Student \"{$student->name}\" added to group.");
    }

    public function removeMember($groupId, $userId)
    {
        $group = Group::with('section.course')->findOrFail($groupId);

        if ($group->section->course->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        $group->members()->detach($userId);

        return back()->with('success', 'Student removed from group.');
    }
}
