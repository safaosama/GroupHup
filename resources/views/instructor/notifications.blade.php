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

    <div class="content-box">

        <h2 style="margin-bottom:15px;">All Notifications</h2>

        @if($notifications->count() > 0)
            <div style="max-height:70vh; overflow-y:auto;">
                @foreach($notifications as $n)
                    <div class="notif-item">
                        <div class="notif-title">{{ $n->title }}</div>
                        <div class="notif-time">
                            {{ $n->created_at->diffForHumans() }}
                        </div>
                        <p class="notif-message">
                            {{ $n->message }}
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color:#9ca3af;">No notifications yet.</p>
        @endif

    </div>

</div>

<style>
.notif-item{
    background:#011736c4;
    padding:12px;
    border-radius:10px;
    border:1px solid rgba(255,255,255,0.06);
    color:#e2e8f0;
    margin-bottom:10px;
}

.notif-title{
    font-size:13px;
    font-weight:600;
}

.notif-time{
    font-size:11px;
    color:#9ca3af;
    margin-top:2px;
}

.notif-message{
    font-size:12px;
    margin-top:5px;
    line-height:1.4;
    color:#cbd5e1;
}

/* sidebar active */
.sidebar ul li.active a{
    color:#fff;
    font-weight:600;
}
</style>

@endsection
