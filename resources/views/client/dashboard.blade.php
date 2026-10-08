<x-client.layout.app title="Dashboard">
@php
    $authUser = Auth::guard('client')->user();
    $orgName = $settings->organization_name ?? $settings->site_name ?? 'Your Organization';
    $currency = $settings->currency ?? '৳';
@endphp

<h3 class="section-title"><i class="fas fa-chart-pie"></i> Dashboard</h3>

<div class="card smart-dashboard-card">
    <div class="flex-between-mb10 items-center">
        <div>
            <div class="text-xs-bold-muted">{{ strtoupper($orgName) }}</div>
            <h3 class="template-name mt-5 mb-0">Organization Overview</h3>
        </div>
        <span class="custom-badge badge-approved">Active</span>
    </div>
    <div class="smart-dashboard-balance">{{ $currency }} {{ number_format($totalBalance) }}</div>
    <div class="text-xs-bold-muted mt-5">Current Balance</div>
    <div class="smart-dashboard-summary">
        <div><strong>{{ number_format($totalMembers) }}</strong><span>Members</span></div>
        <div><strong>{{ number_format($totalProjects) }}</strong><span>Projects</span></div>
        <div><strong>{{ number_format($totalUsers) }}</strong><span>Users</span></div>
    </div>
</div>

<h3 class="section-title mt-20"><i class="fas fa-history"></i> Recent Activity</h3>
<div class="template-list">
    @forelse($recentActivities as $activity)
        <div class="template-item">
            <h4 class="template-name">{{ $activity->description ?? 'Account activity' }}</h4>
            <div class="template-footer">
                <span>{{ $activity->created_at?->diffForHumans() }}</span>
            </div>
        </div>
    @empty
        <div class="smart-empty">No recent activity.</div>
    @endforelse
</div>

<h3 class="section-title mt-20"><i class="fas fa-bolt"></i> Quick Menu</h3>
<div class="menu-list">
    <a href="{{ route('client.members.index') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-users"></i></div><span class="menu-text">Members</span><i class="fas fa-chevron-right menu-arrow"></i></a>
    <a href="{{ route('client.projects.index') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-project-diagram"></i></div><span class="menu-text">Projects</span><i class="fas fa-chevron-right menu-arrow"></i></a>
    <a href="{{ route('client.payments.index') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-credit-card"></i></div><span class="menu-text">Payments</span><i class="fas fa-chevron-right menu-arrow"></i></a>
</div>
</x-client.layout.app>
