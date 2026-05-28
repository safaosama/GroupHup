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

    <a href="/instructor/courses" class="back-link">← Back to Courses</a>

    <div class="topbar">
        <div>
            <h1>{{ $course->name }}</h1>
            <p style="color:var(--text-muted);font-size:13px;margin-top:4px;">
                Group size: {{ $course->min_students }}–{{ $course->max_students }}
                &nbsp;·&nbsp; {{ $course->sections->count() }} section(s)
            </p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
        <a href="/instructor/courses/{{ $course->id }}/report" target="_blank" class="btn-report">
        📋 Print Report
        </a>
            <div class="badge">Instructor</div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    {{-- Add Section --}}
    <div class="content-box">
        <h2>Add Section</h2>
        <form method="POST" action="/courses/{{ $course->id }}/sections" class="form-grid" style="align-items:stretch;">
            @csrf
            <input name="name" placeholder="Section Name" required style="margin-bottom:0;">
            <select name="formation_method" required style="margin-bottom:0;">
                <option value="">Formation Method</option>
                <option value="manual">Manual Groups</option>
                <option value="student_choice">Student Choice</option>
                <option value="random">Random Groups</option>
            </select>
            <input name="group_size" type="number" placeholder="Group Size (2–10)" min="2" max="10" required style="margin-bottom:0;">
            <button type="submit">Add Section</button>
        </form>
    </div>

    {{-- Sections Loop --}}
    @forelse($course->sections as $section)
        @php
            $groupMemberIds = $section->groups->flatMap(fn($g) => $g->members->pluck('id'))->unique();
            $ungrouped      = $section->users->filter(fn($u) => !$groupMemberIds->contains($u->id));
            $hasGroups      = $section->groups->isNotEmpty();
            $isRandom       = $section->formation_method === 'random';
            $randomLocked   = $isRandom && $hasGroups;
            $isChoice       = $section->formation_method === 'student_choice';
        @endphp

        <div class="content-box" style="margin-top:18px;">

            {{-- Section Header --}}
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
                <div>
                    <h2 style="margin-bottom:5px;">{{ $section->name }}</h2>
                    <p style="color:var(--text-muted);font-size:13px;">
                        Method:
                        <strong style="color:{{ $isRandom ? '#f59e0b' : ($isChoice ? '#a78bfa' : '#3b82f6') }}">
                            {{ ucfirst(str_replace('_',' ',$section->formation_method)) }}
                        </strong>
                        &nbsp;·&nbsp; Size: {{ $section->group_size }}
                        &nbsp;·&nbsp; {{ $section->users->count() }} enrolled
                        &nbsp;·&nbsp; {{ $section->groups->count() }} groups
                        &nbsp;·&nbsp; <span style="color:{{ $ungrouped->count() > 0 ? '#ef4444' : '#4ade80' }}">{{ $ungrouped->count() }} ungrouped</span>
                    </p>
                    @if($randomLocked)
                        <p style="color:#f59e0b;font-size:12px;margin-top:5px;">⚠ Random groups locked — manage members below.</p>
                    @endif
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                    {{-- Instructor can ALWAYS create a group --}}
                    <button type="button" class="btn-manage btn-sm"
                        onclick="document.getElementById('new-group-form-{{ $section->id }}').style.display='block';this.style.display='none'">
                        + Create Group
                    </button>
                    {{-- Random generate (only before first generate) --}}
                    @if($isRandom && !$hasGroups && $section->users->isNotEmpty())
                        <form method="POST" action="/sections/{{ $section->id }}/generate-groups">
                            @csrf
                            <button style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#000;font-weight:700;">✦ Random Groups</button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- New Group Form (hidden by default) --}}
            <div id="new-group-form-{{ $section->id }}" style="display:none;background:var(--bg-main);border:1px solid var(--border);border-radius:10px;padding:14px;margin-bottom:16px;">
                <form method="POST" action="/sections/{{ $section->id }}/groups" style="display:flex;gap:8px;align-items:center;">
                    @csrf
                    <input name="name" placeholder="Group name" style="margin-bottom:0;flex:1;" required>
                    <button type="submit" style="flex-shrink:0;">Create</button>
                    <button type="button" class="btn-danger" style="flex-shrink:0;"
                        onclick="document.getElementById('new-group-form-{{ $section->id }}').style.display='none';
                                 document.querySelector('[onclick*=\'new-group-form-{{ $section->id }}\']').style.display=''">
                        Cancel
                    </button>
                </form>
            </div>

            {{-- Add Students --}}
            <div style="background:var(--bg-main);border:1px solid var(--border);border-radius:10px;padding:14px;margin-bottom:18px;">
                <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted);margin-bottom:12px;">Add Students to Section</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <p style="color:var(--text-muted);font-size:12px;margin-bottom:6px;">By Student ID</p>
                        <form method="POST" action="/sections/{{ $section->id }}/add-student" style="display:flex;gap:8px;">
                            @csrf
                            <input name="student_id" placeholder="Student ID" style="margin-bottom:0;flex:1;" required>
                            <button type="submit" style="flex-shrink:0;padding:9px 14px;font-size:13px;">Add</button>
                        </form>
                    </div>
                    <div>
                        <p style="color:var(--text-muted);font-size:12px;margin-bottom:6px;">Upload CSV</p>
                        <form method="POST" action="/sections/{{ $section->id }}/upload-students" enctype="multipart/form-data" style="display:flex;gap:8px;">
                            @csrf
                            <input type="file" name="csv_file" accept=".csv" style="margin-bottom:0;flex:1;" required>
                            <button type="submit" style="flex-shrink:0;padding:9px 14px;font-size:13px;">Upload</button>
                        </form>

                        <div style="margin-top:10px; background: rgba(255,255,255,0.02); padding: 8px; border-radius: 6px; border: 1px dashed rgba(255,255,255,0.05);">
                            <small style="color:var(--text-muted); font-size:12px; display:block; margin-bottom:5px;">
                                <i class="fas fa-info-circle" style="color:#3b82f6;"></i>
                               ℹ Ensure that the CSV file contains the required columns in the correct order.
                            </small>
                            <a href="{{ route('instructor.sections.downloadSample') }}" style="color:#60a5fa; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                📥 Download Sample CSV Template
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Enrolled Students (collapsible) --}}
            @if($section->users->isNotEmpty())
                <details style="margin-bottom:18px;">
                    <summary style="cursor:pointer;font-size:13px;font-weight:600;color:var(--text-secondary);padding:8px 0;user-select:none;list-style:none;display:flex;align-items:center;gap:8px;">
                        <span>▸</span> Enrolled Students ({{ $section->users->count() }})
                    </summary>
                    <div style="margin-top:10px;display:grid;gap:5px;max-height:180px;overflow-y:auto;padding-right:4px;">
                        @foreach($section->users as $user)
                            <div style="display:flex;justify-content:space-between;align-items:center;background:var(--bg-main);border:1px solid var(--border);border-radius:8px;padding:8px 12px;">
                                <div>
                                    <span style="font-size:13px;font-weight:500;">{{ $user->name }}</span>
                                    <span style="color:var(--text-muted);font-size:12px;margin-left:8px;">{{ $user->student_id }}</span>
                                </div>
                                @if($groupMemberIds->contains($user->id))
                                    <span style="font-size:11px;color:#4ade80;">✓ grouped</span>
                                @else
                                    <span style="font-size:11px;color:#ef4444;">ungrouped</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </details>
            @endif

            {{-- Groups --}}
            @if($hasGroups)
                <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted);margin-bottom:10px;">Groups</p>

                @foreach($section->groups as $group)
                    <div class="group-card" style="margin-bottom:12px;">
                        <div class="group-header">
                            <div>
                                <h3>{{ $group->name }}</h3>
                                <p>{{ $group->members->count() }} member(s)</p>
                            </div>
                            <div style="display:flex;gap:8px;align-items:center;">
                                <button type="button" class="toggle-members-btn btn-sm"
                                    data-group-id="{{ $group->id }}">
                                    Group Details
                                </button>
                                <button type="button" class="btn-leave btn-sm"
                                    data-target="deleteGroupModal-{{ $group->id }}">
                                    Delete Group
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Group Details Modal --}}
                    <div id="groupModal{{ $group->id }}" class="modal-overlay" style="display:none;">
                        <div class="modal-box" style="width:500px;">
                            <h3>{{ $group->name }} — Members</h3>
                            <div class="member-grid" style="margin-top:14px;">
                                @forelse($group->members as $member)
                                    @php $teamsEmail = $member->student_id . '@std.jadara.edu.jo'; @endphp
                                    <div class="member-chip">
                                        <div>
                                            <strong>{{ $member->name }}</strong>
                                            <p class="member-subtext">{{ $member->student_id }}</p>
                                        </div>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <a href="https://teams.microsoft.com/l/chat/0/0?users={{ $teamsEmail }}"
                                               target="_blank"
                                               style="color:#60a5fa;font-size:12px;text-decoration:none;border-bottom:1px solid transparent;transition:.2s;"
                                               onmouseover="this.style.borderBottomColor='#60a5fa'"
                                               onmouseout="this.style.borderBottomColor='transparent'">
                                                {{ $teamsEmail }}
                                            </a>
                                            <button type="button" class="btn-leave btn-sm"
                                                data-target="removeMemberModal-{{ $group->id }}-{{ $member->id }}">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <p style="color:var(--text-muted);font-size:13px;">No members yet.</p>
                                @endforelse
                            </div>

                            {{-- Add member inside modal --}}
                            <div style="margin-top:16px;border-top:1px solid rgba(255,255,255,.08);padding-top:14px;">
                                <p style="font-size:12px;color:var(--text-muted);margin-bottom:8px;">Add student to this group:</p>
                                <form method="POST" action="/groups/{{ $group->id }}/add-member" style="display:flex;gap:8px;">
                                    @csrf
                                    <input name="student_id" placeholder="Student ID" style="margin-bottom:0;flex:1;font-size:13px;" required>
                                    <button type="submit" style="flex-shrink:0;padding:9px 14px;font-size:13px;">Add</button>
                                </form>
                            </div>

                            <button class="close-modal-btn" data-group-id="{{ $group->id }}">Close</button>
                        </div>
                    </div>

                    {{-- Delete Group Confirm Modal --}}
                    <div id="deleteGroupModal{{ $group->id }}" class="modal-overlay" style="display:none;">
                        <div class="modal-box" style="text-align:center;width:380px;">
                            <h3>Delete Group</h3>
                            <p style="margin-top:10px;color:var(--text-muted);">
                                Are you sure you want to delete <strong style="color:#f5f7fa;">{{ $group->name }}</strong>?<br>
                                All members will be ungrouped.
                            </p>
                            <div class="modal-actions">
                                <button class="close-btn" data-close-id="deleteGroupModal{{ $group->id }}">Cancel</button>
                                <button class="submit-btn" data-submit-id="del-group-form{{ $group->id }}">Yes, Delete</button>
                            </div>
                        </div>
                    </div>
                    <form id="del-group-form{{ $group->id }}" method="POST" action="/groups/{{ $group->id }}" style="display:none;">
                        @csrf @method('DELETE')
                    </form>

                    {{-- Remove Member Modals --}}
                    @foreach($group->members as $member)
                        <div id="removeMemberModal{{ $group->id }}{{ $member->id }}" class="modal-overlay" style="display:none;">
                            <div class="modal-box" style="text-align:center;width:380px;">
                                <h3>Remove Member</h3>
                                <p style="margin-top:10px;color:var(--text-muted);">
                                    Remove <strong style="color:#f5f7fa;">{{ $member->name }}</strong> from <strong style="color:#f5f7fa;">{{ $group->name }}</strong>?
                                </p>
                                <div class="modal-actions">
                                    <button class="close-btn" data-close-id="removeMemberModal{{ $group->id }}{{ $member->id }}">Cancel</button>
                                    <button class="submit-btn" data-submit-id="rm-form{{ $group->id }}{{ $member->id }}">Yes, Remove</button>
                                </div>
                            </div>
                        </div>
                        <form id="rm-form{{ $group->id }}{{ $member->id }}" method="POST" action="/groups/{{ $group->id }}/members/{{ $member->id }}" style="display:none;">
                            @csrf @method('DELETE')
                        </form>
                    @endforeach

                @endforeach

            @else
                <p style="color:var(--text-muted);font-size:13px;margin-top:4px;">
                    @if($isRandom && $section->users->isEmpty())
                        Upload students first, then generate random groups.
                    @elseif($isChoice)
                        Students will create and join groups themselves. You can still create groups manually above.
                    @else
                        No groups yet. Create the first group above.
                    @endif
                </p>
            @endif

            {{-- Ungrouped --}}
            @if($ungrouped->isNotEmpty())
                <div style="border-top:1px solid var(--border);padding-top:14px;margin-top:14px;">
                    <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.6px;color:#ef4444;margin-bottom:8px;">
                        Ungrouped ({{ $ungrouped->count() }})
                    </p>
                    <div style="display:grid;gap:5px;">
                        @foreach($ungrouped as $student)
                            <div style="background:rgba(239,68,68,.05);border:1px solid rgba(239,68,68,.15);border-radius:8px;padding:8px 12px;font-size:13px;color:#fca5a5;">
                                {{ $student->name }} — {{ $student->student_id }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    @empty
        <div class="content-box" style="text-align:center;padding:48px;margin-top:18px;">
            <p style="color:var(--text-muted);">No sections yet. Add the first section above.</p>
        </div>
    @endforelse

</div>

{{-- ─── STYLES (matching student groups design) ─── --}}
<style>
.back-link {
    display: inline-flex; align-items: center; gap: 5px;
    color: var(--text-muted); font-size: 13px; text-decoration: none;
    margin-bottom: 16px; transition: .2s;
}
.back-link:hover { color: var(--text); }

.toggle-members-btn {
    background: var(--accent); color: white;
    border: none; padding: 6px 10px; border-radius: 6px;
    font-size: 11px; cursor: pointer; height: 32px;
}

.btn-leave {
    background: #790303; color: #fff; border: none;
    padding: 6px 12px; border-radius: 6px;
    font-size: 11px; cursor: pointer; height: 32px;
    display: inline-flex; align-items: center;
}

.btn-sm { height: 32px !important; font-size: 11px !important; padding: 0 12px !important; }

/* Modal - identical to student design */
.modal-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,.4); backdrop-filter: blur(6px);
    display: flex; justify-content: center; align-items: center; z-index: 9999;
}

.modal-box {
    background: #06172dc7; width: 450px; max-height: 80vh;
    overflow-y: auto; padding: 25px; border-radius: 16px;
    text-align: left; box-shadow: 0 20px 60px rgba(0,0,0,.5);
    color: #e2e8f0; border: 1px solid rgba(255,255,255,.08);
}

.modal-box h3 { margin-bottom: 15px; font-size: 18px; color: #f8fafc; }

.member-chip {
    background: #011736c4; padding: 12px; border-radius: 10px;
    border: 1px solid rgba(255,255,255,.06); color: #e2e8f0;
    display: flex; justify-content: space-between; align-items: center;
}
.member-chip strong { color: #fff; }
.member-subtext { color: #6a6a6a; font-size: 12px; margin: 0; }

.member-grid { display: flex; flex-direction: column; gap: 10px; max-height: 300px; overflow-y: auto; }

.close-modal-btn {
    margin-top: 15px; background: #475569; color: white;
    border: none; padding: 8px 14px; border-radius: 8px;
    cursor: pointer; transition: .2s; width: 100%; font-size: 13px;
}
.close-modal-btn:hover { background: #64748b; }

.modal-actions {
    margin-top: 20px; display: flex;
    justify-content: center; gap: 12px;
}
.modal-actions button {
    min-width: 120px; height: 38px; border-radius: 8px;
    font-size: 13px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    border: none;
}
.modal-actions .close-btn { background: #475569; color: white; }
.modal-actions .submit-btn { background: #790303; color: white; }
.modal-actions .close-btn:hover { background: #64748b; }
.modal-actions .submit-btn:hover { background: #991b1b; }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.modal-box { animation: fadeIn .18s ease; }

/* Alert modal */
.alert-modal-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,.4); backdrop-filter: blur(6px);
    display: flex; justify-content: center; align-items: center; z-index: 9999;
}
.alert-modal-box {
    background: #06172dc7; width: 360px; padding: 28px 28px 20px;
    border-radius: 16px; text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,.5);
    color: #e2e8f0; border: 1px solid rgba(255,255,255,.08);
    animation: fadeIn .18s ease;
}
.alert-modal-box p { color: var(--text-muted); font-size: 14px; margin: 10px 0 20px; line-height: 1.5; }
.alert-modal-box button { background: #3b82f6; color: white; border: none; padding: 8px 28px; border-radius: 8px; cursor: pointer; font-size: 13px; }
</style>

{{-- ─── SCRIPTS ─── --}}
<script>
    function openReport() {
    showAlert(`
        <strong>Course:</strong> {{ $course->name }}<br>
        <hr>

        @foreach($course->sections as $section)
            <strong>Section:</strong> {{ $section->name }}<br>
            <strong>Students:</strong> {{ $section->users->count() }}<br>
            <strong>Groups:</strong> {{ $section->groups->count() }}<br>

            @if($section->groups->count())
                <strong>Group Names:</strong><br>
                @foreach($section->groups as $group)
                    - {{ $group->name }} ({{ $group->members->count() }} members)<br>
                @endforeach
            @endif

            <br><br>
        @endforeach
    `);
}
document.addEventListener('DOMContentLoaded', function () {

    // Group Details open
    document.querySelectorAll('.toggle-members-btn[data-group-id]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('groupModal' + this.dataset.groupId).style.display = 'flex';
        });
    });

    // Close group modal
    document.querySelectorAll('.close-modal-btn[data-group-id]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('groupModal' + this.dataset.groupId).style.display = 'none';
        });
    });

    // Open any modal by target id (delete group / remove member)
    document.querySelectorAll('[data-target]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById(this.dataset.target).style.display = 'flex';
        });
    });

    // Close by data-close-id
    document.querySelectorAll('[data-close-id]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById(this.dataset.closeId).style.display = 'none';
        });
    });

    // Submit by data-submit-id
    document.querySelectorAll('[data-submit-id]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById(this.dataset.submitId).submit();
        });
    });

    // Close on overlay click
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function (e) {
            if (e.target === this) this.style.display = 'none';
        });
    });

    // ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
            document.querySelectorAll('.alert-modal-overlay').forEach(m => m.remove());
        }
    });

    // details arrow toggle
    document.querySelectorAll('details').forEach(d => {
        d.addEventListener('toggle', function () {
            const arrow = this.querySelector('summary span');
            if (arrow) arrow.textContent = this.open ? '▾' : '▸';
        });
    });
});

// Alert modal (for report button)
function showAlert(msg) {
    const overlay = document.createElement('div');
    overlay.className = 'alert-modal-overlay';
    overlay.innerHTML = `
        <div class="alert-modal-box">
            <h3 style="font-size:17px;color:#f8fafc;">Notice</h3>
            <p>${msg}</p>
            <button onclick="this.closest('.alert-modal-overlay').remove()">OK</button>
        </div>`;
    document.body.appendChild(overlay);
    overlay.addEventListener('click', function(e) { if (e.target === this) this.remove(); });
}
</script>

@endsection
