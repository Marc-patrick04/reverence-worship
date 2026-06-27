@extends('layouts.app')

@section('title', 'Super Admin Dashboard')
@section('page-title', 'Super Admin Dashboard')

@section('content')
@php
$activeRate = ($stats['total_users'] ?? 0) > 0
? round((($stats['active_users'] ?? 0) / max($stats['total_users'], 1)) * 100)
: 0;
$quickActions = [
['label' => 'Users', 'href' => route('users.index'), 'icon' => 'fa-users', 'color' => 'text-blue-700 bg-blue-50'],
['label' => 'Roles', 'href' => route('roles.index'), 'icon' => 'fa-user-tag', 'color' => 'text-blue-700 bg-blue-50'],
['label' => 'Permissions', 'href' => route('permission-manager.index'), 'icon' => 'fa-shield-alt', 'color' => 'text-blue-700 bg-blue-50'],
['label' => 'Settings', 'href' => route('settings.index'), 'icon' => 'fa-cog', 'color' => 'text-slate-700 bg-slate-100'],
];

$attentionItems = [
[
'label' => 'Pending Users',
'value' => $stats['pending_users'] ?? 0,
'note' => 'Accounts waiting for approval',
'href' => route('users.index') . '?status=pending',
'icon' => 'fa-user-clock',
'color' => 'text-blue-700 bg-blue-50'
],
[
'label' => 'Inactive Users',
'value' => $stats['inactive_users'] ?? 0,
'note' => 'Disabled accounts to review',
'href' => route('users.index') . '?status=inactive',
'icon' => 'fa-user-slash',
'color' => 'text-slate-700 bg-slate-100'
],
[
'label' => 'Permission Requests',
'value' => $stats['pending_permission_requests'] ?? 0,
'note' => 'Discipline requests pending',
'href' => '/discipline/permission?status=pending',
'icon' => 'fa-envelope-open-text',
'color' => 'text-blue-700 bg-blue-50'
],
[
'label' => 'Roles Configured',
'value' => $stats['total_roles'] ?? 0,
'note' => 'Access groups in the system',
'href' => route('permission-manager.index'),
'icon' => 'fa-shield-alt',
'color' => 'text-blue-700 bg-blue-50'
],
];
@endphp

<div class="super-admin-dashboard max-w-7xl mx-auto px-3 sm:px-4 lg:px-5 py-3 sm:py-4">
    <div class="dashboard-hero flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between mb-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-100">System administration</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Dashboard</h1>
            <p class="text-sm text-blue-50 mt-1">Welcome back, {{ Auth::user()->name }}. Monitor access, users, approvals, and system records.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('logs.activity') }}" class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg border border-white/20 bg-white/10 text-sm font-semibold text-white hover:bg-white/15">
                <i class="fas fa-clock"></i>
                Activity Logs
            </a>
            <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-white text-blue-800 text-sm font-semibold hover:bg-blue-50">
                <i class="fas fa-user-plus"></i>
                Manage Users
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
        <div class="admin-kpi">
            <div>
                <p class="admin-kpi-label">Total Users</p>
                <p class="admin-kpi-value">{{ number_format($stats['total_users'] ?? 0) }}</p>
                <p class="admin-kpi-note">{{ number_format($stats['new_users_month'] ?? 0) }} new this month</p>
            </div>
            <span class="admin-kpi-icon"><i class="fas fa-users"></i></span>
        </div>

        <div class="admin-kpi">
            <div>
                <p class="admin-kpi-label">Active Users</p>
                <p class="admin-kpi-value">{{ number_format($stats['active_users'] ?? 0) }}</p>
                <p class="admin-kpi-note">{{ $activeRate }}% of all users</p>
            </div>
            <span class="admin-kpi-icon"><i class="fas fa-user-check"></i></span>
        </div>

        <div class="admin-kpi">
            <div>
                <p class="admin-kpi-label">Families</p>
                <p class="admin-kpi-value">{{ number_format($stats['total_families'] ?? 0) }}</p>
                <p class="admin-kpi-note">{{ number_format($stats['total_members'] ?? 0) }} family members</p>
            </div>
            <span class="admin-kpi-icon"><i class="fas fa-home"></i></span>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[1.35fr_0.65fr] gap-4 mb-4">
        <section class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2 class="admin-panel-title">System Attention</h2>
                    <p class="admin-panel-subtitle">Important items that may need admin review</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4">
                @foreach($attentionItems as $item)
                <a href="{{ $item['href'] }}" class="attention-item">
                    <span class="attention-icon {{ $item['color'] }}">
                        <i class="fas {{ $item['icon'] }}"></i>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-bold text-gray-900">{{ $item['label'] }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ $item['note'] }}</span>
                    </span>
                    <span class="attention-value">{{ number_format($item['value']) }}</span>
                </a>
                @endforeach
            </div>
        </section>

        <section class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2 class="admin-panel-title">Quick Actions</h2>
                    <p class="admin-panel-subtitle">Common admin tasks</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 p-4">
                @foreach($quickActions as $action)
                <a href="{{ $action['href'] }}" class="quick-action">
                    <span class="quick-action-icon {{ $action['color'] }}">
                        <i class="fas {{ $action['icon'] }}"></i>
                    </span>
                    <span>{{ $action['label'] }}</span>
                </a>
                @endforeach
            </div>
        </section>
    </div>

    <section class="admin-panel mt-4">
        <div class="admin-panel-header">
            <div>
                <h2 class="admin-panel-title">System Counts</h2>
                <p class="admin-panel-subtitle">Records across the main modules</p>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-5 gap-2.5 p-3">
            <div class="system-count"><span>{{ number_format($stats['total_roles'] ?? 0) }}</span>
                <p>Roles</p>
            </div>
            <div class="system-count"><span>{{ number_format($stats['total_pages'] ?? 0) }}</span>
                <p>Pages</p>
            </div>
            <div class="system-count"><span>{{ number_format($stats['total_forms'] ?? 0) }}</span>
                <p>Forms</p>
            </div>
            <div class="system-count"><span>{{ number_format($stats['total_devotions'] ?? 0) }}</span>
                <p>Devotions</p>
            </div>
            <div class="system-count"><span>{{ number_format($stats['total_songs'] ?? 0) }}</span>
                <p>Songs</p>
            </div>
            <div class="system-count"><span>{{ number_format($stats['total_playlists'] ?? 0) }}</span>
                <p>Playlists</p>
            </div>
            <div class="system-count"><span>{{ number_format($stats['total_sponsors'] ?? 0) }}</span>
                <p>Sponsors</p>
            </div>
            <div class="system-count"><span>{{ number_format($stats['total_announcements'] ?? 0) }}</span>
                <p>Announcements</p>
            </div>
            <div class="system-count"><span>{{ number_format($stats['total_payment_records'] ?? 0) }}</span>
                <p>Payments</p>
            </div>
            <div class="system-count"><span>{{ number_format($stats['total_expense_records'] ?? 0) }}</span>
                <p>Expenses</p>
            </div>
            <div class="system-count"><span>{{ number_format($stats['total_discipline'] ?? 0) }}</span>
                <p>Discipline</p>
            </div>
            <div class="system-count"><span>{{ number_format($stats['total_permissions'] ?? 0) }}</span>
                <p>Requests</p>
            </div>
        </div>
    </section>
</div>

<style>
    .super-admin-dashboard {
        color: #111827;
    }

    .dashboard-hero {
        border-radius: 1rem;
        padding: 1rem;
        background: linear-gradient(100deg, #1d4ed8 0%, #1d4ed8 60%, #1d4ed8 100%);
        border: 1px solid rgba(148, 163, 184, 0.22);
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.16);
    }

    .admin-kpi,
    .admin-panel {
        background: #ffffff;
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 0.75rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .admin-kpi {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.75rem;
    }

    .admin-kpi-label {
        font-size: 0.68rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
    }

    .admin-kpi-value {
        margin-top: 0.25rem;
        font-size: 1.35rem;
        line-height: 1;
        font-weight: 800;
        color: #0f172a;
    }

    .admin-kpi-note {
        margin-top: 0.25rem;
        font-size: 0.7rem;
        color: #64748b;
    }

    .admin-kpi-icon,
    .quick-action-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .admin-kpi-icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.65rem;
        color: #1d4ed8;
        background: #eff6ff;
        border: 1px solid #dbeafe;
    }

    .admin-panel {
        overflow: hidden;
    }

    .admin-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.75rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.18);
        background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
    }

    .admin-panel-title {
        font-size: 0.92rem;
        font-weight: 800;
        color: #0f172a;
    }

    .admin-panel-subtitle {
        margin-top: 0.125rem;
        font-size: 0.7rem;
        color: #64748b;
    }

    .admin-link-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        border-radius: 0.5rem;
        border: 1px solid #cbd5e1;
        background: #fff;
        padding: 0.45rem 0.7rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: #1e3a8a;
    }

    .quick-action {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        border: 1px solid #dbeafe;
        border-radius: 0.65rem;
        padding: 0.6rem;
        font-size: 0.8rem;
        font-weight: 700;
        color: #0f172a;
        transition: background-color 0.15s ease, border-color 0.15s ease;
    }

    .quick-action:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .quick-action-icon {
        width: 2rem;
        height: 2rem;
        border-radius: 0.6rem;
        border: 1px solid rgba(37, 99, 235, 0.12);
    }

    .attention-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: 1px solid #dbeafe;
        border-radius: 0.65rem;
        padding: 0.65rem;
        transition: background-color 0.15s ease, border-color 0.15s ease;
    }

    .attention-item:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .attention-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.15rem;
        height: 2.15rem;
        border-radius: 0.65rem;
        flex-shrink: 0;
        border: 1px solid rgba(37, 99, 235, 0.12);
    }

    .attention-value {
        font-size: 1.05rem;
        line-height: 1;
        font-weight: 800;
        color: #0f172a;
    }

    .system-count {
        border: 1px solid #dbeafe;
        border-radius: 0.65rem;
        padding: 0.6rem;
        text-align: center;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }

    .system-count:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .system-count span {
        display: block;
        font-size: 1.05rem;
        line-height: 1;
        font-weight: 800;
        color: #0f172a;
    }

    .system-count p {
        margin-top: 0.25rem;
        font-size: 0.68rem;
        font-weight: 700;
        color: #64748b;
    }

    @media (max-width: 640px) {
        .admin-panel-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .admin-link-button {
            width: 100%;
        }

        .admin-kpi-value {
            font-size: 1.2rem;
        }
    }
</style>
@endsection