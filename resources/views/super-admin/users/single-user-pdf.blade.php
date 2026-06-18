<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Details - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
        }
        .header h1 {
            font-size: 22px;
            color: #1f2937;
            margin: 0;
        }
        .header p {
            font-size: 12px;
            color: #6b7280;
            margin: 5px 0 0;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        .grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .grid-row {
            display: table-row;
        }
        .grid-label {
            display: table-cell;
            width: 35%;
            padding: 6px 10px 6px 0;
            font-weight: 500;
            color: #6b7280;
            font-size: 11px;
            border-bottom: 1px solid #f3f4f6;
        }
        .grid-value {
            display: table-cell;
            padding: 6px 0;
            font-weight: 500;
            color: #1f2937;
            font-size: 11px;
            border-bottom: 1px solid #f3f4f6;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-active {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
        }
        .badge-verified {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-unverified {
            background: #fef3c7;
            color: #92400e;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #9ca3af;
        }
        .roles-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .role-tag {
            background: #dbeafe;
            color: #1e40af;
            padding: 2px 10px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>User Details</h1>
        <p>Generated on: {{ $generated_date }}</p>
    </div>

    <!-- Profile Header -->
    <div class="section">
        <div class="grid">
            <div class="grid-row">
                <div class="grid-label">Full Name</div>
                <div class="grid-value"><strong>{{ $user->name }}</strong></div>
            </div>
            <div class="grid-row">
                <div class="grid-label">Email Address</div>
                <div class="grid-value">{{ $user->email }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-label">Status</div>
                <div class="grid-value">
                    <span class="badge {{ $user->is_active ? 'badge-active' : 'badge-inactive' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Information -->
    <div class="section">
        <div class="section-title">Personal Information</div>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-label">Phone Number</div>
                <div class="grid-value">{{ $user->phone ?? '-' }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-label">Gender</div>
                <div class="grid-value">{{ ucfirst($user->gender ?? '-') }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-label">Date of Birth</div>
                <div class="grid-value">{{ $user->date_of_birth ? date('F j, Y', strtotime($user->date_of_birth)) : '-' }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-label">Marital Status</div>
                <div class="grid-value">{{ ucfirst($user->marital_status ?? '-') }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-label">Membership Type</div>
                <div class="grid-value">{{ ucfirst($user->membership_type ?? '-') }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-label">Occupation</div>
                <div class="grid-value">{{ $user->occupation ?? '-' }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-label">Ministry Role</div>
                <div class="grid-value">{{ $user->ministry_role ?? '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Address Information -->
    <div class="section">
        <div class="section-title">Address Information</div>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-label">Province</div>
                <div class="grid-value">{{ $user->province ?? '-' }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-label">District</div>
                <div class="grid-value">{{ $user->district ?? '-' }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-label">Sector</div>
                <div class="grid-value">{{ $user->sector ?? '-' }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-label">Village</div>
                <div class="grid-value">{{ $user->village ?? '-' }}</div>
            </div>
            
        </div>
    </div>

    <!-- Emergency Contact -->
    @if($user->emergency_name || $user->emergency_contact)
    <div class="section">
        <div class="section-title">Emergency Contact</div>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-label">Emergency Name</div>
                <div class="grid-value">{{ $user->emergency_name ?? '-' }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-label">Emergency Contact</div>
                <div class="grid-value">{{ $user->emergency_contact ?? '-' }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Roles -->
    <div class="section">
        <div class="section-title">Roles & Permissions</div>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-label">Assigned Roles</div>
                <div class="grid-value">
                    @if($user->roles && $user->roles->count() > 0)
                        <div class="roles-container">
                            @foreach($user->roles as $role)
                                <span class="role-tag">{{ $role->display_name ?? $role->name }}</span>
                            @endforeach
                        </div>
                    @else
                        No roles assigned
                    @endif
                </div>
            </div>
            @if($user->isSuperAdmin())
            <div class="grid-row">
                <div class="grid-label">Super Admin</div>
                <div class="grid-value">
                    <span class="badge badge-active">Yes</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Additional Information -->
    @if($user->skills || $user->notes || $user->singer_notes)
    <div class="section">
        <div class="section-title">Additional Information</div>
        <div class="grid">
            @if($user->skills)
            <div class="grid-row">
                <div class="grid-label">Skills</div>
                <div class="grid-value">{{ $user->skills }}</div>
            </div>
            @endif
            @if($user->notes)
            <div class="grid-row">
                <div class="grid-label">Notes</div>
                <div class="grid-value">{{ $user->notes }}</div>
            </div>
            @endif
            @if($user->singer_notes)
            <div class="grid-row">
                <div class="grid-label">Singer Notes</div>
                <div class="grid-value">{{ $user->singer_notes }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Singer Information -->
    @if($user->is_singer)
    <div class="section">
        <div class="section-title">Singer Information</div>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-label">Voice Part</div>
                <div class="grid-value">{{ $user->voice_part ?? '-' }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-label">Singer Level</div>
                <div class="grid-value">{{ $user->singer_level ?? '-' }}</div>
            </div>
        </div>
    </div>
    @endif

    

    <div class="footer">
        {{ config('app.name') }} - System Generated Report
    </div>
</body>
</html>