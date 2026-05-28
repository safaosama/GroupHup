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

    <div class="content-box">

        <h2 style="margin-bottom:15px;">All Notifications</h2>

        <div style="max-height:70vh; overflow-y:auto;">

            @foreach($notifications as $n)
                <div class="notif-item" style="margin-bottom:10px;">
                    <div class="notif-title">{{ $n->title }}</div>
                    <div class="notif-time">{{ $n->created_at->diffForHumans() }}</div>
                    <p style="font-size:12px; margin-top:5px;">
                        {{ $n->message }}
                    </p>
                </div>
            @endforeach

        </div>

    </div>

</div>
<style>
    .notif-item{
    background:#011736c4;
    padding:12px;
    border-radius:10px;
    border:1px solid rgba(255,255,255,0.06);
    color:#e2e8f0;
}

.notif-title{
    font-size:13px;
    font-weight:600;
}

.notif-time{
    font-size:11px;
    color:#9ca3af;
}

.group-buttons-row{
    display:flex;
    align-items:center;
    gap:10px;
    margin-top:12px;
}
.modal-box{
    background: #06172dc7;
    width: 450px;
    max-height: 80vh;
    overflow-y: auto;
    padding: 25px;
    border-radius: 16px;
    text-align: left;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    color: #e2e8f0;
    border: 1px solid rgba(255,255,255,0.08);
}


.modal-box h3{
    margin-bottom: 15px;
    font-size: 18px;
    color: #f8fafc;
}

.member-chip{
    background: #011736c4;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.06);
    color: #e2e8f0;
}
</style>
@endsection
