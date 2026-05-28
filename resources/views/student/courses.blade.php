@extends('layouts.app')

@section('content')

<div class="sidebar">

    <h2>GroupHup</h2>

    <ul>

        <!-- Dashboard -->
        <li class="{{ request()->is('student') ? 'active' : '' }}">
            <a href="/student">
                Dashboard
            </a>
        </li>

        <!-- Courses -->
        <li class="{{ request()->is('student/courses*') ? 'active' : '' }}">
            <a href="/student/courses">
                My Courses
            </a>
        </li>

        <!-- Groups -->
        <li class="{{ request()->is('student/groups*') ? 'active' : '' }}">
            <a href="/student/groups">
                My Groups
            </a>
        </li>


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
        <h1>My Courses</h1>
        <div class="badge">Student</div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <div class="content-box">
        <h2>Enrolled Courses</h2>
        @if($courses->isEmpty())
            <p style="color:var(--text-muted); margin-top:8px; font-size:13px;">You are not enrolled in any courses yet.</p>
        @else
            <div class="course-grid">
                @foreach($courses as $course)
                    @php
                        $mySections = $user->sections->where('course_id', $course->id);
                    @endphp
                    <div class="course-card collapsible-card" onclick="toggleCard(this)">
                        {{-- Card Header --}}
                        <div class="course-header">
                            <div>
                                <h3>{{ $course->name }}</h3>
                                <p>{{ $mySections->count() }} section(s)</p>
                            </div>
                            <span class="collapse-arrow">▾</span>
                        </div>

                        {{-- Section badges --}}
                        <div class="course-sections">
                            @foreach($mySections as $section)
                                <div class="section-badge">{{ $section->name }}</div>
                            @endforeach
                        </div>

                        {{-- Expanded Details --}}
                        <div class="card-details" style="display:none;">
                            <div class="details-divider"></div>

                            {{-- Coordinator --}}
                            <div class="detail-row">
                                <span class="detail-label">Coordinator</span>
                                <span class="detail-value">
                                    @if($course->user)
                                        {{ $course->user->name }}
                                        @if($course->user->teams_email)
                                            <a href="mailto:{{ $course->user->teams_email }}"
                                               style="color:var(--accent); font-size:11px; display:block; margin-top:2px;"
                                               onclick="event.stopPropagation()">
                                                📧 {{ $course->user->teams_email }}
                                            </a>
                                        @endif
                                    @else
                                        <span style="color:var(--text-muted);">—</span>
                                    @endif
                                </span>
                            </div>

                            {{-- Group size --}}
                            <div class="detail-row">
                                <span class="detail-label">Group Size</span>
                                <span class="detail-value">{{ $course->min_students }}–{{ $course->max_students }} students</span>
                            </div>

                            {{-- Per-section group status --}}
                            @foreach($mySections as $section)
                                <div class="detail-row" style="align-items:flex-start;">
                                    <span class="detail-label">{{ $section->name }}</span>
                                    <div class="detail-value">
                                        @php
                                            $myGroup = $section->groups->first(fn($g) => $g->members->contains('id', $user->id));
                                        @endphp
                                        @if($myGroup)
                                            <span style="color:#4ade80; font-size:11px; font-weight:600;">
                                                ✓ {{ $myGroup->name }}
                                            </span>

                                            <span style="
                                                background:rgba(59,130,246,0.12);
                                                color:#60a5fa;
                                                padding:3px 7px;
                                                border-radius:999px;
                                                font-size:10px;
                                                margin-left:6px;
                                            ">
                                                {{ $myGroup->members->count() }} Members
                                            </span>
                                            <button
                                            type="button"
                                            class="toggle-members-btn"
                                            onclick="event.stopPropagation(); toggleMembers(event, 'group-{{ $myGroup->id }}')">
                                            Group Details
                                        </button>
                                        <div id="group-{{ $myGroup->id }}"
                                            class="group-members-box"
                                            style="display:none; margin-top:8px;">

                                            @foreach($myGroup->members as $member)
                                                <div class="mini-member {{ $member->id === $user->id ? 'mini-member-self' : '' }}">
                                                    <span>{{ $member->name }}</span>

                                                    @if($member->teams_email)
                                                        <a href="mailto:{{ $member->teams_email }}"
                                                        style="color:var(--accent); font-size:10px; margin-left:6px;"
                                                        onclick="event.stopPropagation()">
                                                            Teams ↗
                                                        </a>
                                                    @endif
                                                </div>
                                            @endforeach

                                        </div>
                                        @else
                                            <span style="color:#f87171; font-size:11px;">Not in a group yet</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<style>
.collapsible-card { cursor: pointer; user-select: none; }
.collapsible-card:hover { border-color: rgba(59,130,246,0.35); }
.course-header { display: flex; justify-content: space-between; align-items: flex-start; }
.collapse-arrow { color: var(--text-muted); font-size: 13px; transition: transform 0.2s; flex-shrink:0; margin-top:2px; }
.collapsible-card.open .collapse-arrow { transform: rotate(180deg); }
.details-divider { border-top: 1px solid var(--border); margin: 10px 0; }
.detail-row { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:5px 0; }
.detail-label { color:var(--text-muted); font-weight:600; font-size:10px; text-transform:uppercase; letter-spacing:0.4px; flex-shrink:0; }
.detail-value { color:var(--text); font-size:12px; text-align:right; }
.mini-member { display:flex; justify-content:space-between; align-items:center; background:var(--bg-card); border:1px solid var(--border); border-radius:5px; padding:4px 8px; font-size:11px; margin-bottom:3px; }
.mini-member-self { border-color:rgba(59,130,246,0.3); background:rgba(59,130,246,0.05); }
.toggle-members-btn{background: var(--accent);color:white;border:none;padding:6px 10px;border-radius:6px;font-size:11px;cursor:pointer;margin-top:6px;}
.toggle-members-btn:hover{opacity:0.9;}
.group-members-box{animation: fadeIn 0.2s ease;}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(-3px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}
</style>

<script>
function toggleCard(card) {
    const details = card.querySelector('.card-details');
    const isOpen = card.classList.contains('open');
    card.classList.toggle('open', !isOpen);
    details.style.display = isOpen ? 'none' : 'block';
}
function toggleMembers(event, id) {
    event.stopPropagation();

    const box = document.getElementById(id);

    if (box.style.display === 'none') {
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}

</script>
@endsection
