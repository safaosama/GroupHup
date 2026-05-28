@extends('layouts.app')

@section('content')

<div class="sidebar">
    <h2>GroupHup</h2>
    <ul>
        <li class="{{ request()->is('instructor') ? 'active' : '' }}"><a href="/instructor">Dashboard</a></li>
        <li class="{{ request()->is('instructor/courses*') ? 'active' : '' }}"><a href="/instructor/courses">My Courses</a></li>
        <li>
            <form method="POST" action="/logout">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </li>
    </ul>
</div>

<div class="main">

    <div class="topbar">
        <h1>My Courses</h1>
        <div style="display:flex;gap:12px;align-items:center;">
            @php
                $unread = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count();
            @endphp
            <a href="/instructor/notifications" style="position:relative;color:var(--text-muted);font-size:18px;text-decoration:none;">
                🔔
                @if($unread > 0)
                    <span style="position:absolute;top:-4px;right:-4px;background:#790303;color:white;font-size:9px;border-radius:50%;width:16px;height:16px;display:flex;align-items:center;justify-content:center;font-weight:700;">{{ $unread }}</span>
                @endif
            </a>
            <div class="badge">Instructor</div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-error">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <div class="content-box">
        <h2>Create New Course</h2>
        <form method="POST" action="/courses" class="form-grid" style="align-items:stretch;">
            @csrf
            <input name="name" placeholder="Course Name" style="margin-bottom:0;" required>
            <input name="min_students" type="number" placeholder="Min per Group" min="1" required style="margin-bottom:0;">
            <input name="max_students" type="number" placeholder="Max per Group" min="1" required style="margin-bottom:0;">
            <button type="submit">Create Course</button>
        </form>
    </div>

    @forelse($courses as $course)
        @php
            $totalStudents = $course->sections->sum(fn($s) => $s->users->count());
            $totalGroups   = $course->sections->sum(fn($s) => $s->groups->count());
        @endphp

        <div class="content-box" style="margin-top:16px;">

            {{-- Row 1: Name (left) — Stats (right) --}}
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:8px;">
                <h2 style="font-size:18px;margin:0;">{{ $course->name }}</h2>
                <div style="display:flex;gap:7px;flex-shrink:0;">
                    <div class="mini-stat">
                        <strong>{{ $course->sections->count() }}</strong><span>Sections</span>
                    </div>
                    <div class="mini-stat">
                        <strong>{{ $totalGroups }}</strong><span>Groups</span>
                    </div>
                    <div class="mini-stat">
                        <strong>{{ $totalStudents }}</strong><span>Students</span>
                    </div>
                </div>
            </div>

            {{-- Row 2: Group size (left) — Buttons (right) --}}
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:10px;">
                <p style="color:var(--text-muted);font-size:13px;margin:0;">
                    Group size: {{ $course->min_students }}–{{ $course->max_students }}
                </p>
                <div style="display:flex;gap:8px;flex-shrink:0;">
                    <a href="/instructor/courses/{{ $course->id }}" class="btn-manage">Manage →</a>
                    <button class="btn-danger"
                        onclick="confirmAction('Delete &ldquo;{{ $course->name }}&rdquo; and all its data?', function(){
                            document.getElementById('del-course-{{ $course->id }}').submit();
                        })">🗑 Delete</button>
                    <form id="del-course-{{ $course->id }}" method="POST" action="/courses/{{ $course->id }}" style="display:none;">
                        @csrf @method('DELETE')
                    </form>
                </div>
            </div>

            {{-- Section Badges --}}
            @if($course->sections->isNotEmpty())
                <div style="display:flex;flex-wrap:wrap;gap:6px;padding-top:10px;border-top:1px solid var(--border);">
                    @foreach($course->sections as $section)
                        <span style="background:var(--bg-main);border:1px solid var(--border);border-radius:20px;padding:3px 11px;font-size:11px;color:var(--text-muted);">
                            {{ $section->name }}
                            <span style="color:var(--text);margin-left:3px;">· {{ $section->users->count() }}</span>
                        </span>
                    @endforeach
                </div>
            @endif

        </div>
    @empty
        <div class="content-box" style="text-align:center;padding:48px;margin-top:16px;">
            <p style="color:var(--text-muted);">No courses yet. Create your first course above.</p>
        </div>
    @endforelse

</div>

<style>
.mini-stat {
    display:flex;align-items:center;gap:6px;
    background:var(--bg-main);border:1px solid var(--border);
    border-radius:8px;padding:5px 12px;font-size:13px;
}
.mini-stat strong { font-weight:700;color:var(--text); }
.mini-stat span   { color:var(--text-muted);font-size:11px; }
</style>

{{-- Confirm Modal --}}
<div id="confirm-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#06172dc7;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:28px;width:380px;max-width:90vw;box-shadow:0 20px 60px rgba(0,0,0,.5);animation:fadeIn .18s ease;">
        <h3 style="font-size:17px;font-weight:600;margin-bottom:10px;color:#f8fafc;">Confirm</h3>
        <p id="confirm-msg" style="color:#8892a6;font-size:14px;margin-bottom:22px;line-height:1.5;"></p>
        <div style="display:flex;gap:10px;justify-content:center;">
            <button onclick="closeConfirm()" style="min-width:110px;height:38px;border-radius:8px;background:#475569;color:white;border:none;font-size:13px;cursor:pointer;">Cancel</button>
            <button id="confirm-ok" style="min-width:110px;height:38px;border-radius:8px;background:#790303;color:#fff;border:none;font-size:13px;font-weight:600;cursor:pointer;">Yes, Delete</button>
        </div>
    </div>
</div>
<style>@keyframes fadeIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}</style>
<script>
function confirmAction(msg,cb){
    document.getElementById('confirm-msg').innerHTML=msg;
    document.getElementById('confirm-modal').style.display='flex';
    document.getElementById('confirm-ok').onclick=function(){closeConfirm();cb()};
}
function closeConfirm(){document.getElementById('confirm-modal').style.display='none';}
document.getElementById('confirm-modal').addEventListener('click',function(e){if(e.target===this)closeConfirm();});
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeConfirm();});
</script>

@endsection
