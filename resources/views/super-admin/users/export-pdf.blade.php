<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Users Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11px;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 10px;
            color: #666;
        }
        
        .summary {
            margin-bottom: 15px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .summary-box {
            background: #f3f4f6;
            padding: 8px 15px;
            border-radius: 6px;
            border-left: 3px solid #2563eb;
        }
        
        .summary-box span {
            font-size: 10px;
            color: #666;
        }
        
        .summary-box strong {
            font-size: 14px;
            color: #1e40af;
            margin-left: 8px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th {
            background: #2563eb;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #1e40af;
        }
        
        td {
            padding: 6px;
            border: 1px solid #d1d5db;
            vertical-align: top;
            font-size: 9px;
        }
        
        tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
        }
        
        .badge-active {
            background: #dcfce7;
            color: #16a34a;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
        }
        
        .badge-inactive {
            background: #fee2e2;
            color: #dc2626;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
        }
        
        .badge-pending {
            background: #fef3c7;
            color: #d97706;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Users Report</h1>
        <p>Generated on: {{ $generated_date }}</p>
    </div>
    
    <div class="summary">
        <div class="summary-box">
            <span>Total Users:</span>
            <strong>{{ $total_users }}</strong>
        </div>
        <div class="summary-box">
            <span>Active:</span>
            <strong>{{ $active_users }}</strong>
        </div>
        <div class="summary-box">
            <span>Inactive:</span>
            <strong>{{ $inactive_users }}</strong>
        </div>
        <div class="summary-box">
            <span>Pending:</span>
            <strong>{{ $pending_users }}</strong>
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
                <th>Occupation</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?? '-' }}</td>
                <td>
                    @foreach($user->roles as $role)
                        {{ $role->display_name }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </td>
                <td>
                    @if($user->is_active)
                        <span class="badge-active">Active</span>
                    @elseif($user->created_by === null && $user->email_verified_at === null)
                        <span class="badge-pending">Pending</span>
                    @else
                        <span class="badge-inactive">Inactive</span>
                    @endif
                </td>
                <td>{{ $user->gender ?? '-' }}</td>
                <td>{{ $user->occupation ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">No users found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p>Reverence Worship Team - User Management Report</p>
    </div>
</body>
</html>