@extends('layouts.app')

@section('content')
    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>

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

    <div class="main" style="direction:ltr;">
        <div class="topbar">
            <h1>Welcome, {{ auth()->user()->name }}</h1>

            <div style="display:flex; gap:12px; align-items:center;">
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
                                <div class="notif-title">{{ $n->title }}</div>
                                <div class="notif-message">{{ $n->message }}</div>
                                <div class="notif-time">{{ $n->created_at->diffForHumans() }}</div>
                            </div>
                        @empty
                            <p class="empty-notif">
                                No notifications
                            </p>
                        @endforelse

                        <div class="notif-footer">
                            <a href="/instructor/notifications">
                                View All
                            </a>
                        </div>
                    </div>
                </div>

                <span class="badge">Instructor</span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif

        <div class="profile-card" style="text-align: left;">
            <div class="profile-content">
                <div>
                    <h2 style="font-family:'Space Grotesk',sans-serif; font-size:16px; font-weight:700; margin-bottom:4px;">
                        {{ auth()->user()->name }}
                    </h2>
                    <p style="color:var(--text-secondary); font-size:12px;">
                        ID: {{ auth()->user()->student_id }}
                    </p>
                </div>

                <div class="stats-grid" style="direction: ltr; text-align: left;">
                    <div class="stat-card">
                        <span style="font-size:11px;">Courses</span>
                        <strong style="font-size:18px;">{{ $coursesCount }}</strong>
                    </div>
                    <div class="stat-card">
                        <span style="font-size:11px;">Groups</span>
                        <strong style="font-size:18px;">{{ $groupsCount }}</strong>
                    </div>
                    <div class="stat-card">
                        <span style="font-size:11px;">Students</span>
                        <strong style="font-size:18px;">{{ $studentsCount }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-box" style="text-align: left;">
            <h2 style="font-size:15px; margin-bottom:14px;">Recent Notifications</h2>
            @if($notifications->isEmpty())
                <p style="color:var(--text-muted); margin-top:10px; font-size:13px;">
                    No recent notifications.
                </p>
            @else
                <div class="notification-list">
                    @foreach($notifications as $notification)
                        <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}"
                             style="padding:12px; border-radius:8px; direction: ltr; text-align: left;">

                            <div class="notification-header" style="margin-bottom:6px;">
                                <h4 style="font-size:13px; font-weight:600; margin:0;">
                                    {{ $notification->title }}
                                </h4>
                                <small style="font-size:11px; color:var(--text-muted);">
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>
                            </div>

                            <p style="font-size:12px; margin:6px 0;">
                                {{ $notification->message }}
                            </p>

                            @if(!$notification->read_at)
                                <form method="POST"
                                      action="/notifications/{{ $notification->id }}/mark-read"
                                      style="display:inline;">
                                    @csrf
                                    <button type="submit"
                                            class="btn-mark-read"
                                            style="font-size:11px; padding:6px 10px;">
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

    <style>
        .notif-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 30px;
            width: 320px;
            background: #06172dc7;
            backdrop-filter: blur(8px);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            z-index: 999;
            padding: 10px;
            color: #e2e8f0;
        }

        .notif-item {
            padding: 10px;
            border-radius: 10px;
            background: #011736c4;
            margin-bottom: 8px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .notif-title {
            font-size: 12px;
            font-weight: 600;
            color: #fff;
        }

        .notif-time {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 3px;
        }

        .notif-footer {
            text-align: center;
            margin-top: 10px;
        }

        .notif-footer a {
            font-size: 12px;
            color: #0558bd;
            text-decoration: none;
        }

        .notif-icon {
            position: relative;
            font-size: 20px;
            cursor: pointer;
        }

        .dot {
            position: absolute;
            top: 0;
            right: 0;
            width: 8px;
            height: 8px;
            background: #790303;
            border-radius: 50%;
        }
    </style>

    <script>
        function toggleNotif() {
            let dropdown = document.getElementById('notifDropdown');
            if (dropdown.style.display === 'block') {
                dropdown.style.display = 'none';
            } else {
                dropdown.style.display = 'block';
            }
        }

        document.addEventListener('click', function (e) {
            let dropdown = document.getElementById('notifDropdown');
            if (
                !e.target.closest('#notifDropdown') &&
                !e.target.closest('.notif-icon')
            ) {
                dropdown.style.display = 'none';
            }
        });
    </script>
@endsection
