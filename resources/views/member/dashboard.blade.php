<x-member.layout.app title="Member Dashboard">
@php
    $member = Auth::guard('member')->user();
    $currency = $settings->currency ?? '৳';
    $organization = $settings->organization_name ?? $settings->site_name ?? 'SMART DPS';
@endphp

<h3 class="section-title"><i class="fas fa-chart-pie"></i> Member Dashboard</h3>

<div class="card smart-dashboard-card">
    <div class="flex-between-mb10 items-center">
        <div>
            <div class="text-xs-bold-muted">{{ strtoupper($organization) }}</div>
            <h3 class="template-name mt-5 mb-0">{{ $member->name }}</h3>
            <div class="smart-meta"><span>{{ $member->member_id }}</span><span class="smart-dot"></span><span>{{ $member->status ? 'Active Member' : 'Inactive Member' }}</span></div>
        </div>
        <span class="custom-badge {{ $member->status ? 'badge-approved' : 'badge-rejected' }}">{{ $member->status ? 'Active' : 'Inactive' }}</span>
    </div>

    <div class="smart-dashboard-balance">{{ $currency }} {{ number_format($totalBalance) }}</div>
    <div class="text-xs-bold-muted mt-5">Current Balance</div>

    <div class="smart-dashboard-summary">
        <div><strong>{{ number_format($totalShares) }}</strong><span>Total Shares</span></div>
        <div><strong>{{ $currency }} {{ number_format($monthlySavings) }}</strong><span>Monthly Savings</span></div>
        <div><strong>{{ number_format($recentPayments->count()) }}</strong><span>Recent Payments</span></div>
    </div>
</div>

<h3 class="section-title mt-20"><i class="fas fa-history"></i> Recent Payments</h3>
<div class="template-list">
    @forelse($recentPayments as $payment)
        <div class="template-item">
            <div class="flex-between-mb10 items-center">
                <div>
                    <h4 class="template-name">{{ $currency }} {{ number_format($payment->amount) }}</h4>
                    <div class="smart-meta">
                        <span>{{ $payment->payment_id }}</span>
                        <span class="smart-dot"></span>
                        <span>{{ $payment->payment_method?->label() ?? 'Payment' }}</span>
                    </div>
                </div>
                <span class="smart-status {{ $payment->status?->value ?? 'pending' }}">{{ $payment->status?->label() ?? 'Pending' }}</span>
            </div>
            <div class="template-footer">
                <span>{{ $payment->paid_at?->format('d M Y') ?? $payment->created_at?->format('d M Y') }}</span>
                @if($payment->reference_number)<span>Ref: {{ $payment->reference_number }}</span>@endif
            </div>
        </div>
    @empty
        <div class="smart-empty">No payment records yet.</div>
    @endforelse
</div>

<h3 class="section-title mt-20"><i class="fas fa-bolt"></i> Quick Menu</h3>
<div class="menu-list">
    <a href="{{ route('member.payment.create') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-upload"></i></div><span class="menu-text">Submit Payment</span><i class="fas fa-chevron-right menu-arrow"></i></a>
    <button type="button" class="menu-item menu-item-logout w-full" data-member-logout style="width:100%;border-top:0;border-left:0;border-right:0;background:transparent;text-align:left"><div class="menu-icon menu-icon-logout"><i class="fas fa-sign-out-alt"></i></div><span class="menu-text">Sign Out</span><i class="fas fa-chevron-right menu-arrow"></i></button>
</div>
</x-member.layout.app>
