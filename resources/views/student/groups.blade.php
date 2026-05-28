@extends('layouts.app')

@section('content')
<div class="sidebar">
    <h2>Student Panel</h2>
    <ul>
        <!-- Dashboard -->
        <li class="{{ request()->is('student') ? 'active' : '' }}">
            <a href="/student">Dashboard</a>
        </li>

        <!-- Courses -->
        <li class="{{ request()->is('student/courses*') ? 'active' : '' }}">
            <a href="/student/courses">My Courses</a>
        </li>

        <!-- Groups -->
        <li class="{{ request()->is('student/groups*') ? 'active' : '' }}">
            <a href="/student/groups">My Groups</a>
        </li>

        <!-- Logout -->
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
        <h1>My Groups</h1>
        <div class="badge">Student</div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <!-- Current Groups -->
    <div class="content-box">
        <h2>My Current Groups</h2>
        @if($groups->isEmpty())
            <p style="color:var(--text-muted); margin-top:10px;">
                You haven't joined any groups yet.
            </p>
        @else
            <div class="group-grid">
                @foreach($groups as $group)
                    <div class="group-card">
                        <div class="group-header">
                            <div>
                                <h3>{{ $group->name }}</h3>
                                <p>{{ $group->section->course->name }} / {{ $group->section->name }}</p>
                            </div>
                            <span class="group-members-count">{{ $group->members->count() }} members</span>
                        </div>

                        <div class="group-buttons-row">
                            <button type="button"
                                    class="toggle-members-btn"
                                    data-group-id="{{ $group->id }}">
                                Group Details
                            </button>

                            @if(!$group->is_random)
                                <form id="leaveForm-{{ $group->id }}" method="POST" action="/groups/{{ $group->id }}/leave" class="leave-form">
                                    @csrf
                                    <button type="button"
                                            class="btn-leave"
                                            data-group-id="{{ $group->id }}">
                                        Leave Group
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Available Groups -->
    <div class="content-box">
        <h2>Available Groups</h2>
        @if($availableGroups->isEmpty())
            <p style="color:var(--text-muted); margin-top:10px;">
                No groups are currently available in your sections.
            </p>
        @else
            <div class="group-grid">
                @foreach($availableGroups as $g)
                    @php
                        $isFull = $g->members_count >= $g->section->group_size;
                    @endphp

                    <div class="group-card" style="opacity: {{ $isFull ? '0.7' : '1' }};">
                        <div class="group-header">
                            <div>
                                <h3>{{ $g->name }}</h3>
                                <p>{{ $g->section->course->name }} / {{ $g->section->name }}</p>
                            </div>

                            <div style="text-align:right;">
                                <span>{{ $g->members_count }}/{{ $g->section->group_size }}</span>

                                @if($isFull)
                                    <span style="background:#790303; color:white; padding:4px 8px; border-radius:6px;">
                                        FULL
                                    </span>
                                @endif
                            </div>
                        </div>

                        <form method="POST" action="/groups/{{ $g->id }}/join">
                            @csrf
                            <button class="btn-join" {{ $isFull ? 'disabled' : '' }}>
                                {{ $isFull ? 'Group Full' : 'Join Group' }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
.group-buttons-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 12px;
}

.btn-leave {
    border: none;
    background: #790303;
    color: #fff;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 11px;
    cursor: pointer;
    height: 32px;
    display: flex;
    align-items: center;
}

.toggle-members-btn {
    background: var(--accent);
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 11px;
    cursor: pointer;
    height: 32px;
}

.btn-join {
    width: 100%;
    border: none;
    background: var(--accent);
    color: white;
    padding: 10px;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    height: 40px;
}

.btn-join:disabled {
    background: #6b7280;
    cursor: not-allowed;
}

.group-members-count {
    font-size: 13px;
    color: var(--text-muted);
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(6px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.modal-box {
    background: #06172dc7;
    width: 450px;
    max-height: 80vh;
    overflow-y: auto;
    padding: 25px;
    border-radius: 16px;
    text-align: left;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    color: #e2e8f0;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.modal-box h3 {
    margin-bottom: 15px;
    font-size: 18px;
    color: #f8fafc;
}

.member-chip {
    background: #011736c4;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.06);
    color: #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.member-chip strong {
    color: #ffffff;
}

.member-subtext {
    color: #6a6a6a;
    font-size: 12px;
    margin: 0;
}

.member-chip .member-subtext:last-child {
    margin-left: auto;
}

.close-modal-btn {
    margin-top: 15px;
    background: #790303;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.2s;
    width: 100%;
}

.close-modal-btn:hover {
    background: #475569;
}

.modal-actions {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    gap: 12px;
}

.modal-actions button {
    min-width: 120px;
    height: 38px;
    border-radius: 8px;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: #790303;
    color: white;
}

.modal-actions button:first-child {
    background: #475569;
}

.member-grid {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-height: 400px;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-3px); }
    to { opacity: 1; transform: translateY(0); }
}

.group-members-box {
    animation: fadeIn 0.2s ease;
}
</style>

{{-- Group Modals --}}
@foreach($groups as $group)
<div id="groupModal-{{ $group->id }}" class="modal-overlay" style="display: none;">
    <div class="modal-box">
        <h3>{{ $group->name }} - Members</h3>
        <div class="member-grid">
            @foreach($group->members as $member)
                <div class="member-chip {{ isset($user) && $member->id === $user->id ? 'member-self' : '' }}">
                    <div>
                        <strong>{{ $member->name }}</strong>
                        <p class="member-subtext">{{ $member->student_id }}</p>
                    </div>
                    <p class="member-subtext">
                        <a href="mailto:{{ $member->teams_email }}" style="color: #60a5fa; text-decoration: none;">
                            {{ $member->teams_email }}
                        </a>
                    </p>
                </div>
            @endforeach
        </div>
        <button class="close-modal-btn" data-group-id="{{ $group->id }}">
            Close
        </button>
    </div>
</div>
@endforeach

{{-- Leave Modals --}}
@foreach($groups as $group)
    @if(!$group->is_random)
    <div id="leaveModal-{{ $group->id }}" class="modal-overlay" style="display: none;">
        <div class="modal-box" style="text-align: center;">
            <h3>Leave Group</h3>
            <p style="margin-top: 10px;">
                Are you sure you want to leave <strong>{{ $group->name }}</strong>?
            </p>
            <div class="modal-actions">
                <button class="close-btn" data-close-id="leaveModal-{{ $group->id }}">
                    Cancel
                </button>
                <button class="submit-btn" data-submit-id="leaveForm-{{ $group->id }}">
                    Yes, Leave
                </button>
            </div>
        </div>
    </div>
    @endif
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Group Details Modal
    document.querySelectorAll('.toggle-members-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const groupId = this.dataset.groupId;
            document.getElementById('groupModal-' + groupId).style.display = 'flex';
        });
    });

    // Close Group Modal
    document.querySelectorAll('.close-modal-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const groupId = this.dataset.groupId;
            document.getElementById('groupModal-' + groupId).style.display = 'none';
        });
    });

    // Leave Group Modal
    document.querySelectorAll('.btn-leave[data-group-id]').forEach(btn => {
        btn.addEventListener('click', function() {
            const groupId = this.dataset.groupId;
            document.getElementById('leaveModal-' + groupId).style.display = 'flex';
        });
    });

    // Modal Actions
    document.querySelectorAll('[data-close-id]').forEach(btn => {
        btn.addEventListener('click', function() {
            const modalId = this.dataset.closeId;
            document.getElementById(modalId).style.display = 'none';
        });
    });

    document.querySelectorAll('[data-submit-id]').forEach(btn => {
        btn.addEventListener('click', function() {
            const formId = this.dataset.submitId;
            document.getElementById(formId).submit();
        });
    });

    // Close modals on overlay click
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    });
});
</script>
@endsection
