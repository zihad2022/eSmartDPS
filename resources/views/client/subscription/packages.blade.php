<x-client.layout.app title="Subscription Packages">
@php
    $client = \App\Models\Client::with(['activeClientPackage.package', 'latestClientPackage.package'])->findOrFail(owner_client_id());
    $clientPackage = $client->activeClientPackage ?? $client->latestClientPackage;
    $activePackageId = $clientPackage?->package_id;
    $isExpired = $clientPackage?->ends_at && now()->greaterThan($clientPackage->ends_at);
@endphp
<h3 class="section-title"><i class="fas fa-crown"></i> Choose Your Plan</h3>
<div class="pricing-grid">
@foreach($packages as $package)
    @php
        $isCurrent = $activePackageId === $package->id;
        $discountedPrice = $package->price;
        if ($package->discount_value > 0) {
            $discountedPrice = $package->discount_type === \App\Enums\Package\DiscountType::FIXED
                ? $package->price - $package->discount_value
                : $package->price - ($package->price * $package->discount_value / 100);
            $discountedPrice = max(0, $discountedPrice);
        }
        $featured = !$isCurrent && $package->price > 0 && $loop->index === 1;
    @endphp
    <div class="pricing-card {{ $featured ? 'premium' : '' }}">
        @if($featured)<div class="popular-badge">Popular</div>@endif
        @if($isCurrent)<div class="current-plan-pill">Current</div>@endif
        <h4 class="plan-name">{{ $package->name }}</h4>
        @if($package->discount_value > 0)<div class="plan-old">{{ $settings->currency }} {{ number_format($package->price) }}</div>@endif
        <div class="plan-price">{{ $settings->currency }} {{ number_format($discountedPrice) }}<span>/ {{ $package->billing_cycle?->label() ?? 'month' }}</span></div>
        <div class="feature-list-pricing">
            <div class="feature-item-pricing"><i class="fas fa-check-circle"></i> Members: {{ $package->member_limit ?: 'Unlimited' }}</div>
            <div class="feature-item-pricing"><i class="fas fa-check-circle"></i> Users: {{ $package->user_limit ?: 'Unlimited' }}</div>
            <div class="feature-item-pricing"><i class="fas fa-check-circle"></i> Projects: {{ $package->project_limit ?: 'Unlimited' }}</div>
            @if($package->has_trial)<div class="feature-item-pricing"><i class="fas fa-check-circle"></i> Trial: {{ $package->trial_days }} days</div>@endif
        </div>
        <div class="plan-actions-sc">
        @if(!$isCurrent)
            <form action="{{ route('client.subscription.start.paid',$package) }}" method="GET"><button class="btn btn-primary" type="submit">Subscribe Now</button></form>
            @if($package->has_trial)<form action="{{ route('client.subscription.start.trial',$package) }}" method="GET"><button class="btn btn-outline" type="submit">Start {{ $package->trial_days }}-Day Free Trial</button></form>@endif
        @elseif($isExpired)
            <form action="{{ route('client.subscription.start.paid',$package) }}" method="GET"><button class="btn btn-primary" type="submit">Renew Plan</button></form>
        @else
            <button class="btn btn-outline" type="button" disabled>Current Plan</button>
        @endif
        </div>
    </div>
@endforeach
</div>
</x-client.layout.app>
