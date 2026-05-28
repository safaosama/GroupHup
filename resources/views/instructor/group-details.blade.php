@extends('layouts.app')

@section('content')

<div class="sidebar">

    <h2>GroupHup</h2>

    <ul>
        <!-- Dashboard -->
        <li class="{{ request()->is('instructor') ? 'active' : '' }}"><a href="/instructor">Dashboard</a></li>

        <!-- Courses -->
        <li class="{{ request()->is('instructor/courses*') ? 'active' : '' }}"><a href="/instructor/courses">My Courses</a></li>

        <!-- Logout -->
        <li>
            <form method="POST" action="/logout">
                @csrf

                <button type="submit">
                    Logout
                </button>
            </form>
        </li>
    </ul>
</div>

<div class="main">

    <div class="topbar">
        <div>
            <a href="/instructor/courses" style="color:var(--text-muted); font-size:13px; text-decoration:none; display:block; margin-bottom:6px;">← Back to Courses</a>
           <h1>{{ $group->name }}</h1>

        </div>
        <span class="badge">Instructor</span>
    </div>

    <div class="content-box">
        <p style="color:var(--text-muted); font-size:14px;">Course: <span style="color:var(--text);">{{ $group->course->name }}</span></p>
    </div>

    <div class="content-box">
        <h2>Members ({{ $group->members->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Student ID</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                @foreach($group->members as $member)
                    <tr>
                        <td>{{ $member->name }}</td>
                        <td style="color:var(--text-muted);">{{ $member->student_id }}</td>
                        <td>
                            @if($member->id === $group->created_by)
                                <span class="leader-badge">Leader</span>
                            @else
                                <span style="color:var(--text-muted); font-size:13px;">Member</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection

