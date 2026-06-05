<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Users Personal Information Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 9px;
            padding: 15px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 8px;
        }
        
        .header h1 {
            font-size: 16px;
            color: #1e40af;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 8px;
            color: #666;
        }
        
        .summary {
            margin-bottom: 12px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .summary-box {
            background: #f3f4f6;
            padding: 6px 12px;
            border-radius: 6px;
            border-left: 3px solid #2563eb;
        }
        
        .summary-box span {
            font-size: 8px;
            color: #666;
        }
        
        .summary-box strong {
            font-size: 12px;
            color: #1e40af;
            margin-left: 8px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7px;
        }
        
        th {
            background: #2563eb;
            color: white;
            padding: 6px 3px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #1e40af;
        }
        
        td {
            padding: 5px 3px;
            border: 1px solid #d1d5db;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 7px;
            color: #666;
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 10px;
            font-size: 6px;
            font-weight: bold;
        }
        
        .badge-active {
            background: #dcfce7;
            color: #16a34a;
        }
        
        .badge-inactive {
            background: #fee2e2;
            color: #dc2626;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>All Users Personal Information Report</h1>
        <p>Generated on: {{ $generated_date }}</p>
    </div>
    
    <div class="summary">
        <div class="summary-box">
            <span>Total Users:</span>
            <strong>{{ $total_users }}</strong>
        </div>
        <div class="summary-box">
            <span>Active Users:</span>
            <strong>{{ $active_users }}</strong>
        </div>
        <div class="summary-box">
            <span>Inactive Users:</span>
            <strong>{{ $inactive_users }}</strong>
        </div>
        <div class="summary-box">
            <span>Pending Users:</span>
            <strong>{{ $pending_users }}</strong>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="25">#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Status</th>
                <th>DOB</th>
                <th>Gender</th>
                <th>Marital Status</th>
                <th>Residence</th>
                <th>Family</th>
                <th>Occupation</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?? '-' }}</td>
                <td>
                    @foreach($user->roles as $role)
                        {{ $role->display_name }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                    @if($user->roles->isEmpty())
                        -
                    @endif
                </td>
                <td>
                    @if($user->is_active)
                        <span class="badge badge-active">Active</span>
                    @else
                        <span class="badge badge-inactive">Inactive</span>
                    @endif
                </td>
                <td>{{ $user->date_of_birth ? date('d/m/y', strtotime($user->date_of_birth)) : '-' }}</td>
                <td>{{ $user->gender ?? '-' }}</td>
                <td>{{ $user->marital_status ?? '-' }}</td>
                <td>
                    @php
                        $residence = [];
                        if ($user->province) $residence[] = $user->province;
                        if ($user->district) $residence[] = $user->district;
                        if ($user->sector) $residence[] = $user->sector;
                        if ($user->village) $residence[] = $user->village;
                        echo implode(', ', $residence) ?: '-';
                    @endphp
                </td>
               <td class="px-6 py-4 text-sm">
    @php
        if (isset($user->family_name) && $user->family_name && $user->family_name != '-') {
            $familyDisplay = $user->family_name;
            if (isset($user->family_role) && $user->family_role && $user->family_role != '-') {
                $familyDisplay .= ' (' . ucfirst($user->family_role) . ')';
            }
            echo $familyDisplay;
        } else {
            echo '-';
        }
    @endphp
</td>
                <td>{{ $user->occupation ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="12" style="text-align: center;">No users found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p>Reverence Worship Team - User Management Report</p>
        <p>This report is system generated and contains all user information based on current filters.</p>
    </div>
</body>
</html>