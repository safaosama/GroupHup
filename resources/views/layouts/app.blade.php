<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GroupHup</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
    :root {
        --bg-main: #0a0e27;
        --bg-sidebar: #0f1229;
        --bg-card: #141829;
        --text: #f5f7fa;
        --text-muted: #8892a6;
        --text-secondary: #a4afbd;
        --border: rgba(255, 255, 255, 0.08);
        --accent: #3b82f6;
        --sidebar-width: 240px;
    }

    * { margin:0; padding:0; box-sizing:border-box; }
    body { background: var(--bg-main); color: var(--text); font-family: 'Inter', sans-serif; font-size: 15px; line-height: 1.6; font-weight: 400; }

    /* SIDEBAR */
    .sidebar {
        width: var(--sidebar-width);
        background: var(--bg-sidebar);
        height: 100vh;
        position: fixed;
        top: 0; left: 0;
        padding: 32px 24px;
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--border);
        z-index: 100;
    }

    .sidebar h2 {
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 32px;
        letter-spacing: -0.3px;
    }

    .sidebar ul { list-style: none; flex: 1; }
    .sidebar ul li { margin-bottom: 8px; }

    .sidebar ul li a {
        color: var(--text-secondary);
        text-decoration: none;
        display: block;
        padding: 12px 14px;
        border-radius: 8px;
        transition: all 0.2s;
        font-size: 14px;
        font-weight: 500;
        border: 1px solid transparent;
    }

    .sidebar ul li a:hover {
        background: rgba(59, 130, 246, 0.1);
        color: var(--text);
        border-color: rgba(59, 130, 246, 0.2);
    }

    .sidebar ul li.active a {
        background: rgba(59, 130, 246, 0.12);
        color: #3b82f6;
        border-color: rgba(59, 130, 246, 0.3);
    }

    /* Logout button in sidebar */
    .sidebar ul li form button,
    .sidebar-logout button {
        width: 100%;
        padding: 12px 14px;
        background: rgba(239, 68, 68, 0.08);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.2);
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        font-family: 'Inter', sans-serif;
        text-align: left;
        transition: all 0.2s;
    }

    .sidebar ul li form button:hover,
    .sidebar-logout button:hover {
        background: rgba(239, 68, 68, 0.16);
        border-color: rgba(239, 68, 68, 0.3);
    }

    .sidebar ul li:last-child {
        margin-top: auto;
        padding-top: 18px;
        border-top: 1px solid var(--border);
        position: absolute;
        bottom: 32px;
        left: 24px;
        right: 24px;
    }

    /* MAIN */
    .main { margin-left: var(--sidebar-width); padding: 40px 48px; min-height: 100vh; }

    .topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 36px;
    }

    .topbar h1 {
        font-size: 32px;
        font-weight: 700;
        letter-spacing: -0.8px;
    }

    .badge {
        background: rgba(59, 130, 246, 0.12);
        color: #3b82f6;
        padding: 6px 16px;
        border-radius: 24px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.4px;
        border: 1px solid rgba(59, 130, 246, 0.25);
        text-transform: uppercase;
    }

    /* ALERTS */
    .alert-success {
        background: rgba(34, 197, 94, 0.09);
        border: 1px solid rgba(34, 197, 94, 0.25);
        color: #4ade80;
        padding: 14px 18px;
        border-radius: 8px;
        margin-bottom: 24px;
        font-size: 14px;
        font-weight: 500;
    }

    .alert-error {
        background: rgba(239, 68, 68, 0.09);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #ef4444;
        padding: 14px 18px;
        border-radius: 8px;
        margin-bottom: 24px;
        font-size: 14px;
        font-weight: 500;
    }

    /* CARDS */
    .card {
        background: var(--bg-card);
        padding: 20px;
        border-radius: 10px;
        border: 1px solid var(--border);
    }

    .card h3 { color: var(--text-muted); font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 10px; }
    .card p  { font-size: 28px; font-weight: 700; }
    .form-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        align-items: stretch;
    }

    .form-grid button {
        width: 100%;
        height: 100%;
    }

    .content-box {
        background: var(--bg-card);
        padding: 28px;
        border-radius: 12px;
        border: 1px solid var(--border);
        margin-top: 24px;
    }

    .content-box h2 {
        margin-bottom: 20px;
        font-size: 18px;
        font-weight: 700;
    }

    .content-box h3 {
        margin-bottom: 14px;
        font-size: 16px;
        font-weight: 600;
    }

    /* TABLE */
    table { width: 100%; border-collapse: collapse; }
    th {
        text-align: left;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 12px 14px;
        border-bottom: 1px solid var(--border);
    }
    td {
        padding: 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        font-size: 14px;
        color: var(--text);
    }
    tr:last-child td { border-bottom: none; }

    /* FORMS */
    input, select, textarea {
        width: 100%;
        padding: 12px 14px;
        margin-bottom: 14px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--bg-main);
        color: var(--text);
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        transition: border-color 0.2s;
        outline: none;
    }

    input:focus, select:focus { border-color: rgba(59, 130, 246, 0.5); }
    input::placeholder { color: var(--text-muted); }
    input[type="file"] { padding: 11px 14px; cursor: pointer; color: var(--text-secondary); }

    /* BUTTONS */
    button {
        padding: 11px 20px;
        border-radius: 8px;
        background: #3b82f6;
        color: white;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        font-weight: 600;
    }

    button:hover { background: #2563eb; }

    .btn-danger {
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.25);
    }
    .btn-danger:hover { background: rgba(239, 68, 68, 0.2); }

    .btn-join { background: #3b82f6; color: white; border: none; }
    .btn-join:hover { background: #2563eb; }

    .btn-manage {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        padding: 10px 18px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid rgba(59, 130, 246, 0.25);
        transition: all 0.2s;
    }

    .btn-manage:hover { background: rgba(59, 130, 246, 0.18); color: white; text-decoration: none; }

    .btn-report {
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.25);
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        transition: .2s;
    }

    .btn-report:hover {
        background: rgba(34, 197, 94, 0.18);
    }

    .btn-mark-read {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        border: 1px solid rgba(59, 130, 246, 0.25);
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
    }

    .btn-mark-read:hover { background: rgba(59, 130, 246, 0.18); }

    .action-buttons {
        display: flex;
        gap: 14px;
        margin-top: 18px;
        flex-wrap: wrap;
    }

    .btn-action {
        background: #3b82f6;
        color: white;
        padding: 12px 22px;
        border-radius: 8px;
        text-decoration: none;
        transition: .2s;
        display: inline-block;
        font-size: 14px;
        font-weight: 600;
    }

    .btn-action:hover {
        background: #2563eb;
        text-decoration: none;
        color: white;
    }

    .course-grid {
        display: grid;
        gap: 20px;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        margin-top: 18px;
    }

    .course-card {
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 22px;
        transition: border-color 0.2s;
    }

    .course-card:hover {
        border-color: rgba(59, 130, 246, 0.3);
    }

    .course-header h3 {
        margin: 0 0 8px 0;
        font-size: 16px;
        font-weight: 600;
    }

    .course-header p {
        color: var(--text-muted);
        margin: 0;
        font-size: 13px;
    }

    .course-sections {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 14px;
    }

    .section-badge {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.25);
        border-radius: 6px;
        padding: 5px 12px;
        font-size: 12px;
        color: #3b82f6;
        font-weight: 500;
    }

    .btn-leave {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.25);
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        transition: .2s;
    }

    .btn-leave:hover {
        background: rgba(239, 68, 68, 0.18);
    }

    /* FORM GRID */
    .form-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        align-items: end;
    }

    .form-row { display: grid; gap: 14px; margin-bottom: 16px; }

    /* PROFILE CARD */
    .profile-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 28px;
        margin-bottom: 26px;
    }

    .profile-content {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 28px;
        flex-wrap: wrap;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 14px;
        flex: 1;
        min-width: 280px;
    }

    .stat-card {
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 16px 18px;
    }

    .stat-card span {
        display: block;
        color: var(--text-muted);
        margin-bottom: 8px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-card strong {
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
    }

    .profile-card > div:first-child h2 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .profile-card p {
        color: var(--text-muted);
        font-size: 14px;
        margin-bottom: 8px;
    }

    /* GROUP CARDS */
    .group-grid {
        display: grid;
        gap: 18px;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        margin-top: 18px;
    }

    .group-card {
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 20px;
        transition: border-color 0.2s;
    }

    .group-card:hover {
        border-color: rgba(59, 130, 246, 0.3);
    }

    .group-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
    }

    .group-header h3 { font-size: 15px; font-weight: 600; margin-bottom: 4px; }
    .group-header p { color: var(--text-muted); font-size: 13px; }
    .group-header span {
        color: var(--text-muted);
        font-size: 12px;
        background: var(--bg-card);
        padding: 5px 11px;
        border-radius: 20px;
        border: 1px solid var(--border);
        white-space: nowrap;
    }

    /* NOTIFICATIONS */
    .notification-list { display: grid; gap: 12px; margin-top: 16px; }

    .notification-item {
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 16px;
        transition: border-color 0.2s;
    }

    .notification-item.unread { border-color: rgba(59, 130, 246, 0.3); background: rgba(59, 130, 246, 0.05); }

    .notification-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
    .notification-header h4 { margin: 0; font-size: 14px; font-weight: 600; }
    .notification-header small { color: var(--text-muted); font-size: 12px; }
    .notification-item p { margin: 8px 0 10px; color: var(--text-secondary); font-size: 13px; }

    /* MEMBER CHIPS */
    .member-chip {
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 12px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        font-size: 13px;
    }

    .member-chip strong { font-weight: 600; }
    .member-subtext { color: var(--text-muted); font-size: 12px; }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
            padding: 18px 24px;
            flex-direction: row;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
        }
        .sidebar h2 { margin-bottom: 0; }
        .sidebar ul { display: flex; flex-wrap: wrap; gap: 8px; }
        .sidebar ul li { margin-bottom: 0; }
        .sidebar ul li:last-child { position: static; border-top: none; padding-top: 0; }
        .main { margin-left: 0; padding: 26px 24px; }
    }

    @media (max-width: 600px) {
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .profile-content { flex-direction: column; }
        .topbar { flex-direction: column; align-items: flex-start; gap: 12px; }
        table { font-size: 13px; }
        th, td { padding: 10px 8px; }
        .main { padding: 20px 16px; }
    }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
<script>
setTimeout(() => {
    const alert = document.querySelector('.alert-success');
    if (alert) {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }
}, 3000);
</script>
