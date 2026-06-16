<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Users Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3b82f6;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 12px;
            color: #4b5563;
            margin-bottom: 5px;
        }
        .date {
            font-size: 10px;
            color: #6b7280;
            margin-top: 5px;
        }
        .summary-box {
            margin-top: 15px;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f3f4f6;
            border-radius: 5px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .summary-label {
            font-weight: bold;
            color: #374151;
        }
        .summary-value {
            color: #1f2937;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #f3f4f6;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #d1d5db;
        }
        td {
            padding: 6px;
            border: 1px solid #d1d5db;
            font-size: 9px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }
        .status-active {
            color: #10b981;
            font-weight: bold;
        }
        .status-inactive {
            color: #ef4444;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Users Report</div>
        <div class="subtitle">{{ config('app.name', 'Reverence Worship') }}</div>
        <div class="date">Generated on: {{ $generated_date }}</div>
        <div class="date">Period: {{ $start_date }} - {{ $end_date }}</div>
    </div>
    
    <div class="summary-box">
        <div class="summary-row">
            <span class="summary-label">Total Users:</span>
            <span class="summary-value">{{ $total_users }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Active Users:</span>
            <span class="summary-value">{{ $active_users }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Inactive Users:</span>
            <span class="summary-value">{{ $inactive_users }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Male / Female:</span>
            <span class="summary-value">{{ $male_users }} / {{ $female_users }}</span>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Status</th>
                <th>Gender</th>
                <th>Registered Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?? '-' }}</td>
                <td>{{ $user->roles->first()->display_name ?? 'No Role' }}</td>
                <td>
                    @if($user->is_active)
                        <span class="status-active">Active</span>
                    @else
                        <span class="status-inactive">Inactive</span>
                    @endif
                </td>
                <td>{{ $user->gender ?? '-' }}</td>
                <td>{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">No users found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        This report is system-generated. For any inquiries, please contact the system administrator.
    </div>
</body>
</html>