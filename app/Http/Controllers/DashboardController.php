<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Group;

class DashboardController extends Controller
{
    public function student()
    {
        if (auth()->user()->role !== 'student') {
            return redirect('/instructor');
        }

        $user = auth()->user()->load(['sections.course', 'groups.section.course', 'groups.members']);

        $courses = $user->sections->map->course->unique('id')->values();
        $groups  = $user->groups;
        $notifications = $user->notifications()->latest()->take(5)->get();
        $unreadCount   = $user->notifications()->whereNull('read_at')->count();

        return view('student.dashboard', compact('user', 'courses', 'groups', 'notifications', 'unreadCount'));
    }

    public function studentCourses()
    {
        if (auth()->user()->role !== 'student') return redirect('/instructor');

        $user = auth()->user()->load(['sections.course', 'sections.groups' => function($q) {
            $q->withCount('members');
        }]);

        $courses = $user->sections->map->course->unique('id')->values();

        return view('student.courses', compact('user', 'courses'));
    }

    public function studentGroups()
    {
        if (auth()->user()->role !== 'student') return redirect('/instructor');

        $user = auth()->user()->load(['sections.course', 'groups.section.course', 'groups.members']);
        $groups = $user->groups;
        $availableGroups = Group::whereHas('section', function ($q) use ($user) {
            $q->whereIn('id', $user->sections->pluck('id'))
            ->where('formation_method', 'student_choice');
        })
        ->with('section.course')
        ->whereDoesntHave('members', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->get()
        ->map(function ($group) {
            $group->members_count = $group->members->count();
            return $group;
        });

        return view('student.groups', compact('user','groups', 'availableGroups'));
    }

    public function instructor()
    {
        $user = auth()->user();

        $courses = \App\Models\Course::where('user_id', $user->id)->get();
        $coursesCount = $courses->count();

        $groupsCount = \App\Models\Group::whereHas('section.course', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        $studentsCount = \DB::table('section_user')
            ->join('sections', 'section_user.section_id', '=', 'sections.id')
            ->join('courses', 'sections.course_id', '=', 'courses.id')
            ->where('courses.user_id', $user->id)
            ->distinct()
            ->count('section_user.user_id');

        $notifications = \App\Models\Notification::where('user_id', $user->id)->latest()->take(5)->get();
        $unreadNotificationsCount = \App\Models\Notification::where('user_id', $user->id)->whereNull('read_at')->count();

        return view('instructor.dashboard', compact(
            'courses', 'coursesCount', 'groupsCount', 'studentsCount', 'notifications', 'unreadNotificationsCount'
        ));
    }

    public function instructorNotifications()
    {
        if (auth()->user()->role !== 'instructor') return redirect('/student');

        return view('instructor.notifications', [
            'notifications' => auth()->user()->notifications()->latest()->get()
        ]);
    }

    public function instructorCourses()
    {
        if (auth()->user()->role !== 'instructor') return redirect('/student');

        $courses = Course::where('user_id', auth()->id())
                        ->with([
                            'sections' => function($q) {
                                $q->with(['users', 'groups' => function($qq) {
                                    $qq->withCount('members');
                                }]);
                            }
                        ])
                        ->get();

        return view('instructor.courses', compact('courses'));
    }

    public function generateReport($courseId)
    {
        $course = \App\Models\Course::with(['sections.groups.members', 'sections.users'])->findOrFail($courseId);
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        return view('instructor.course-report', compact('course'));
    }

    public function instructorCourseDetails($courseId)
    {
        if (auth()->user()->role !== 'instructor') {
            return redirect('/student');
        }

        $course = Course::where('user_id', auth()->id())
                        ->with(['users', 'sections.groups.members', 'sections.users'])
                        ->findOrFail($courseId);

        return view('instructor.course-details', compact('course'));
    }

    public function markNotificationRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    public function studentNotifications()
    {
        $user = auth()->user();

        return view('student.notifications', [
            'user' => $user,
            'notifications' => $user->notifications()->latest()->get()
        ]);
    }
}
