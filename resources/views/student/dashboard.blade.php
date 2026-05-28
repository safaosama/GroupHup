@extends('layouts.app')

@section('content')

<div class="sidebar">

    <h2>Student Panel</h2>

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

    <!-- TOPBAR -->
    <div class="topbar">

        <h1>Welcome, {{ auth()->user()->name }}</h1>
       

        <div style="display:flex; gap:14px; align-items:center;">

            <!-- Notifications -->
            <div style="position:relative;">

                <div onclick="toggleNotif()" style="cursor:pointer; position:relative;">

                    <div class="notif-icon">

                        <i class="fa fa-bell"></i>

                        @if($notifications->whereNull('read_at')->count() > 0)
                            <span class="dot"></span>
                        @endif

                    </div>

                    @if($notifications->whereNull('read_at')->count() > 0)

                        <span class="notif-count">
                            {{ $notifications->whereNull('read_at')->count() }}
                        </span>

                    @endif

                </div>

                <!-- Dropdown -->
                <div id="notifDropdown" class="notif-dropdown">

                    <h3 class="notif-header">Notifications</h3>

                    @forelse($notifications->take(5) as $n)

                        <div class="notif-item">

                            <div class="notif-title">
                                {{ $n->title }}
                            </div>

                            <div class="notif-message">
                                {{ $n->message }}
                            </div>

                            <div class="notif-time">
                                {{ $n->created_at->diffForHumans() }}
                            </div>

                        </div>

                    @empty

                        <p class="empty-notif">
                            No notifications
                        </p>

                    @endforelse

                    <div class="notif-footer">
                        <a href="/student/notifications">
                            View All
                        </a>
                    </div>

                </div>

            </div>

            <span class="badge">Student</span>

        </div>

    </div>

    <!-- ALERTS -->
    @if(session('success'))

        <div class="alert-success">
            {{ session('success') }}
        </div>

    @endif

    @if(session('error'))

        <div class="alert-error">
            {{ session('error') }}
        </div>

    @endif

    <!-- PROFILE -->
    <div class="profile-card">

        <div class="profile-content">

            <div>

                <h2 class="profile-name">
                    {{ $user->name }}
                </h2>

                <p class="profile-id">
                    ID: {{ $user->student_id }}
                </p>

            </div>

            <div class="stats-grid">

                <div class="stat-card">
                    <span>Courses</span>
                    <strong>{{ $courses->count() }}</strong>
                </div>

                <div class="stat-card">
                    <span>Sections</span>
                    <strong>{{ $user->sections->count() }}</strong>
                </div>

                <div class="stat-card">
                    <span>Teams</span>
                    <strong>{{ $groups->count() }}</strong>
                </div>

                <div class="stat-card">
                    <span>Members</span>
                    <strong>
                        {{ $groups->sum(fn($group) => $group->members->count()) }}
                    </strong>
                </div>

            </div>

        </div>

    </div>

    <!-- RECENT NOTIFICATIONS -->
    <div class="content-box">

        <h2 class="section-title">
            Recent Notifications
        </h2>

        @if($notifications->isEmpty())

            <p class="empty-notif">
                No recent notifications.
            </p>

        @else

            <div class="notification-list">

                @foreach($notifications as $notification)

                    <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}">

                        <div class="notification-header">

                            <h4>
                                {{ $notification->title }}
                            </h4>

                            <small>
                                {{ $notification->created_at->diffForHumans() }}
                            </small>

                        </div>

                        <p>
                            {{ $notification->message }}
                        </p>

                        @if(!$notification->read_at)

                            <form method="POST"
                                  action="/notifications/{{ $notification->id }}/mark-read">

                                @csrf

                                <button type="submit" class="btn-mark-read">
                                    Mark as Read
                                </button>

                            </form>

                        @endif

                    </div>

                @endforeach

            </div>

        @endif

    </div>

</div>

<!-- FONT AWESOME -->
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>

:root{
    --bg-main:#0a0e27;
    --bg-sidebar:#0f1229;
    --bg-card:#141829;
    --text:#f5f7fa;
    --text-muted:#8892a6;
    --text-secondary:#a4afbd;
    --border:rgba(255,255,255,0.08);
    --accent:#3b82f6;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:var(--bg-main);
    color:var(--text);
    font-family:Arial,sans-serif;
}

/* SIDEBAR */

.sidebar{
    position:fixed;
    width:240px;
    height:100vh;
    background:var(--bg-sidebar);
    padding:24px;
    border-right:1px solid var(--border);
}

.sidebar h2{
    margin-bottom:28px;
}

.sidebar ul{
    list-style:none;
}

.sidebar li{
    margin-bottom:12px;
}

.sidebar a,
.sidebar button{
    width:100%;
    display:block;
    padding:12px;
    border-radius:10px;
    text-decoration:none;
    color:white;
    background:none;
    border:none;
    text-align:left;
    cursor:pointer;
    transition:0.3s;
}

.sidebar a:hover,
.sidebar button:hover,
.sidebar .active a{
    background:rgba(255,255,255,0.08);
}

/* MAIN */

.main{
    margin-left:240px;
    padding:30px;
}

/* TOPBAR */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:28px;
}

.badge{
    background:rgba(59,130,246,0.2);
    color:#60a5fa;
    padding:8px 14px;
    border-radius:30px;
    font-size:12px;
}

/* NOTIFICATION ICON */

.notif-icon{
    position:relative;
    width:42px;
    height:42px;
    border-radius:12px;
    background:#141b34;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
    transition:0.3s;
}

.notif-icon:hover{
    background:#1d2747;
}

.dot{
    position:absolute;
    top:8px;
    right:8px;
    width:10px;
    height:10px;
    background:#ef4444;
    border-radius:50%;
    border:2px solid #141b34;
}

.notif-count{
    position:absolute;
    top:-5px;
    right:-5px;
    background:#ef4444;
    color:white;
    width:20px;
    height:20px;
    border-radius:50%;
    font-size:11px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
}

/* DROPDOWN */

.notif-dropdown{
    display:none;
    position:absolute;
    top:55px;
    right:0;
    width:330px;
    background:#111827;
    border-radius:18px;
    padding:14px;
    box-shadow:0 20px 50px rgba(0,0,0,0.5);
    z-index:999;
    border:1px solid rgba(255,255,255,0.06);
}

.notif-header{
    margin-bottom:14px;
    font-size:15px;
}

.notif-item{
    padding:12px;
    border-radius:12px;
    background:#18213d;
    margin-bottom:10px;
}

.notif-title{
    font-size:13px;
    font-weight:700;
}

.notif-message{
    font-size:12px;
    color:#cbd5e1;
    margin-top:5px;
}

.notif-time{
    font-size:11px;
    color:#94a3b8;
    margin-top:6px;
}

.notif-footer{
    text-align:center;
    margin-top:12px;
}

.notif-footer a{
    color:#60a5fa;
    text-decoration:none;
    font-size:13px;
}

/* PROFILE */

.profile-card{
    background:var(--bg-card);
    border-radius:20px;
    padding:24px;
    margin-bottom:24px;
    border:1px solid var(--border);
}

.profile-content{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
}

.profile-name{
    font-size:18px;
    margin-bottom:4px;
}

.profile-id{
    color:var(--text-secondary);
    font-size:13px;
}

/* STATS */

.stats-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:12px;
}

.stat-card{
    background:#1a213d;
    padding:16px;
    border-radius:14px;
    text-align:center;
}

.stat-card span{
    display:block;
    font-size:12px;
    color:#94a3b8;
    margin-bottom:6px;
}

.stat-card strong{
    font-size:20px;
}

/* CONTENT */

.content-box{
    background:var(--bg-card);
    padding:24px;
    border-radius:20px;
    border:1px solid var(--border);
}

.section-title{
    margin-bottom:18px;
    font-size:16px;
}

.notification-item{
    background:#18213d;
    padding:16px;
    border-radius:14px;
    margin-bottom:14px;
}

.notification-header{
    display:flex;
    justify-content:space-between;
    margin-bottom:8px;
}

.notification-header h4{
    font-size:14px;
}

.notification-header small{
    color:#94a3b8;
}

.notification-item p{
    font-size:13px;
    color:#d1d5db;
    margin-bottom:10px;
}

.unread{
    border-left:4px solid #3b82f6;
}

.read{
    opacity:0.75;
}

.btn-mark-read{
    background:#2563eb;
    border:none;
    color:white;
    padding:8px 14px;
    border-radius:8px;
    cursor:pointer;
    font-size:12px;
}

.btn-mark-read:hover{
    background:#1d4ed8;
}

/* ALERTS */

.alert-success,
.alert-error{
    padding:14px;
    border-radius:12px;
    margin-bottom:18px;
    font-size:13px;
}

.alert-success{
    background:rgba(34,197,94,0.15);
    color:#4ade80;
}

.alert-error{
    background:rgba(239,68,68,0.15);
    color:#f87171;
}

.empty-notif{
    color:#94a3b8;
    font-size:13px;
}

</style>

<script>

function toggleNotif() {

    let dropdown = document.getElementById('notifDropdown');

    if(dropdown.style.display === 'block'){
        dropdown.style.display = 'none';
    }else{
        dropdown.style.display = 'block';
    }
}

document.addEventListener('click', function(e){

    let dropdown = document.getElementById('notifDropdown');

    if(
        !e.target.closest('#notifDropdown')
        &&
        !e.target.closest('.notif-icon')
    ){
        dropdown.style.display = 'none';
    }

});

</script>

@endsection
