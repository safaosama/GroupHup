<!DOCTYPE html>
<html>
<head>
    <title>Report - {{ $course->name }}</title>
    <style>
        body { font-family: sans-serif; padding: 40px; }
        h1 { border-bottom: 2px solid #333; padding-bottom: 10px; }
        .section { margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <button class="no-print" onclick="window.print()">Print PDF</button>

    <h1>{{ $course->name }} - Final Report</h1>

    @foreach($course->sections as $section)
        <div class="section">
            <h2>Section: {{ $section->name }}</h2>
            <p>Total Students: {{ $section->users->count() }} | Groups: {{ $section->groups->count() }}</p>

            <table>
                <thead>
                    <tr><th>Group Name</th><th>Members</th></tr>
                </thead>
                <tbody>
                    @foreach($section->groups as $group)
                        <tr>
                            <td>{{ $group->name }}</td>
                            <td>{{ $group->members->pluck('name')->implode(', ') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>
