<x-client.layout.app title="Subscription Packages">
@php
    $client = \App\Models\Client::with(['activeClientPackage.package', 'latestClientPackage.package'])->findOrFail(owner_client_id());
    $clientPackage = $client->activeClientPackage ?? $client->latestClientPackage;
    $activePackageId = $clientPackage?->package_id;
    $isExpired = $clientPackage?->ends_at && now()->greaterThan($clientPackage->ends_at);

    $gatewaySettings = \App\Models\AdminSetting::first();
    $bkashActive = $gatewaySettings?->bkash_status ?? true;
    $sslActive = filled($gatewaySettings?->sslcommerz_store_id) || filled(config('payments.sslcommerz.store_id'));
    $activeGatewayCount = ($bkashActive ? 1 : 0) + ($sslActive ? 1 : 0);
    $hasAnyGateway = $activeGatewayCount > 0;
    $defaultGateway = $bkashActive ? 'bkash' : ($sslActive ? 'sslcommerz' : '');
@endphp

<h3 class="section-title"><i class="fas fa-crown"></i> Choose Your Plan</h3>

<div class="pricing-grid">
@foreach($packages as $package)
    @php
        $isCurrent = $activePackageId === $package->id && ! $isExpired;
        $discountedPrice = $package->price;
        if ($package->discount_value > 0) {
            $discountedPrice = $package->discount_type === \App\Enums\Package\DiscountType::FIXED
                ? $package->price - $package->discount_value
                : $package->price - ($package->price * $package->discount_value / 100);
            $discountedPrice = max(0, $discountedPrice);
        }
        $featured = !$isCurrent && $package->price > 0 && $loop->index === 1;
        $billingLabel = $package->billing_cycle?->label() ?? 'month';
        $payRoute = route('client.subscription.start.paid', $package);
        $eligibility = $packageEligibility->get($package->id, ['eligible' => true, 'message' => null, 'issues' => []]);
        $canSwitch = (bool) ($eligibility['eligible'] ?? true);
        $switchError = $eligibility['message'] ?? 'This package is not available for your current account usage.';
    @endphp

    <div class="pricing-card {{ $featured ? 'premium' : '' }}">
        @if($featured)<div class="popular-badge">Popular</div>@endif
        @if($isCurrent)<div class="current-plan-pill">Current</div>@endif

        <h4 class="plan-name">{{ $package->name }}</h4>
        @if($package->discount_value > 0)
            <div class="plan-old">{{ $settings->currency }} {{ number_format($package->price) }}</div>
        @endif
        <div class="plan-price">
            {{ $settings->currency }} {{ number_format($discountedPrice) }}
            <span>/ {{ $billingLabel }}</span>
        </div>

        <div class="feature-list-pricing">
            <div class="feature-item-pricing"><i class="fas fa-check-circle"></i> Members: {{ $package->member_limit ?: 'Unlimited' }}</div>
            <div class="feature-item-pricing"><i class="fas fa-check-circle"></i> Users: {{ $package->user_limit ?: 'Unlimited' }}</div>
            <div class="feature-item-pricing"><i class="fas fa-check-circle"></i> Projects: {{ $package->project_limit ?: 'Unlimited' }}</div>
            @if($package->has_trial)
                <div class="feature-item-pricing"><i class="fas fa-check-circle"></i> Trial: {{ $package->trial_days }} days</div>
            @endif
        </div>

        <div class="plan-actions-sc">
            @if(!$isCurrent)
                @if(!$canSwitch)
                    <button
                        class="btn btn-primary sc-btn package-restriction-trigger"
                        type="button"
                        data-error="{{ $switchError }}"
                    >{{ (float)$discountedPrice <= 0 ? 'Activate Plan' : 'Subscribe Now' }}</button>

                    @if($package->has_trial)
                        <button
                            class="btn btn-outline sc-btn package-restriction-trigger"
                            type="button"
                            data-error="{{ $switchError }}"
                        >Start {{ $package->trial_days }}-Day Free Trial</button>
                    @endif
                @else
                    @if((float)$discountedPrice <= 0)
                        <form action="{{ route('client.subscription.start.paid',$package) }}" method="GET">
                            <button class="btn btn-primary sc-btn" type="submit">Activate Plan</button>
                        </form>
                    @else
                        <button
                            class="btn btn-primary sc-btn subscription-modal-trigger"
                            type="button"
                            onclick="openSubscriptionPaymentModal(this)"
                            data-action="{{ $payRoute }}"
                            data-name="{{ $package->name }}"
                            data-price="{{ $settings->currency }} {{ number_format($discountedPrice) }}"
                            data-members="{{ $package->member_limit ?: 'Unlimited' }}"
                            data-users="{{ $package->user_limit ?: 'Unlimited' }}"
                            data-projects="{{ $package->project_limit ?: 'Unlimited' }}"
                            data-billing="{{ $billingLabel }}"
                        >Subscribe Now</button>
                    @endif

                    @if($package->has_trial)
                        <form action="{{ route('client.subscription.start.trial',$package) }}" method="GET">
                            <button class="btn btn-outline sc-btn" type="submit">Start {{ $package->trial_days }}-Day Free Trial</button>
                        </form>
                    @endif
                @endif
            @else
                <button class="btn btn-outline sc-btn" type="button" disabled>Current Plan</button>
            @endif
        </div>
    </div>
@endforeach
</div>

<!-- Package eligibility error modal -->
<div class="modal-overlay" id="package-restriction-modal" onclick="if(event.target === this) closeModal('package-restriction-modal')">
    <div class="modal-content confirm-modal-card">
        <h3 class="font-900 mb-10">Package unavailable</h3>
        <p class="text-muted mb-20" id="package-restriction-message">Your current usage exceeds this package's limits.</p>
        <button type="button" class="btn btn-primary w-full" onclick="closeModal('package-restriction-modal')">Okay</button>
    </div>
</div>

<!-- SC SMS-style Payment Modal -->
<div class="modal-overlay" id="subscription-payment-modal" onclick="if(event.target === this) closeModal('subscription-payment-modal')">
    <div class="modal-content">
        <div class="modal-header-flex">
            <h3 class="font-900"><i class="fas fa-crown"></i> <span id="subscription-modal-title">Subscription</span></h3>
            <i class="fas fa-times cursor-pointer" onclick="closeModal('subscription-payment-modal')"></i>
        </div>

        <div class="modal-body-box mt-15">
            @if($hasAnyGateway)
                <div class="mb-15">
                    @if($bkashActive)
                        <div id="tab-btn-bkash" class="template-item mb-15 cursor-pointer"
                            @if($activeGatewayCount > 1) onclick="switchSubscriptionGateway('bkash')" @endif
                            style="{{ $defaultGateway === 'bkash' ? 'border-color: var(--primary);' : '' }}">
                            <div class="flex-between-mb0 items-center">
                                <h4 class="template-name mb-0 flex-items-center-gap12">
                                    <img src="{{ asset('scuser/assets/img/gateways/bkash-logo.png') }}" alt="bKash" height="24">
                                    <span>bKash Payment</span>
                                </h4>
                                <span class="custom-badge badge-approved">Merchant</span>
                            </div>
                        </div>
                    @endif

                    @if($sslActive)
                        <div id="tab-btn-ssl" class="template-item mb-15 cursor-pointer"
                            @if($activeGatewayCount > 1) onclick="switchSubscriptionGateway('sslcommerz')" @endif
                            style="{{ $defaultGateway === 'sslcommerz' ? 'border-color: var(--primary);' : '' }}">
                            <div class="flex-between-mb0 items-center">
                                <h4 class="template-name mb-0 flex-items-center-gap12">
                                    <img src="{{ asset('scuser/assets/img/gateways/sslcommerz.png') }}" alt="SSLCommerz" height="20">
                                    <span>SSLCommerz</span>
                                </h4>
                                <span class="custom-badge badge-approved">Bank</span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="template-item mb-15">
                    <div class="flex-between-mb10">
                        <span class="text-xs-bold-muted">Members:</span>
                        <span class="text-xs-bold-800" id="subscription-members">—</span>
                    </div>
                    <div class="flex-between-mb10">
                        <span class="text-xs-bold-muted">Users:</span>
                        <span class="text-xs-bold-800" id="subscription-users">—</span>
                    </div>
                    <div class="flex-between-mb10">
                        <span class="text-xs-bold-muted">Projects:</span>
                        <span class="text-xs-bold-800" id="subscription-projects">—</span>
                    </div>
                    <div class="flex-between-mb0">
                        <span class="text-xs-bold-muted">Billing:</span>
                        <span class="text-xs-bold-800" id="subscription-billing">—</span>
                    </div>
                </div>

                @if($bkashActive)
                    <div id="gateway-box-bkash" class="{{ $defaultGateway === 'bkash' ? '' : 'hidden' }}">
                        <form id="subscription-form-bkash" method="GET">
                            <input type="hidden" name="payment_method" value="bkash">
                            <button type="submit" class="btn btn-primary w-full">
                                Pay <span class="subscription-pay-price">BDT 0</span>
                            </button>
                        </form>
                        <p class="text-muted text-center text-xs mt-10">
                            <i class="fas fa-shield-alt"></i> Secured payment via bKash.
                        </p>
                    </div>
                @endif

                @if($sslActive)
                    <div id="gateway-box-ssl" class="{{ $defaultGateway === 'sslcommerz' ? '' : 'hidden' }}">
                        <form id="subscription-form-ssl" method="GET">
                            <input type="hidden" name="payment_method" value="sslcommerz">
                            <button type="submit" class="btn btn-primary w-full">
                                Pay <span class="subscription-pay-price">BDT 0</span>
                            </button>
                        </form>
                        <p class="text-muted text-center text-xs mt-10">
                            <i class="fas fa-shield-alt"></i> Cards, mobile banking &amp; net banking via SSLCommerz.
                        </p>
                    </div>
                @endif
            @else
                <div class="text-center pt-20 pb-20">
                    <p class="text-muted">Online payment is temporarily unavailable. Please check back shortly.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function showClientModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function hideClientModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Keep compatibility with the existing inline close buttons on this page.
    window.closeModal = window.closeModal || hideClientModal;

    function switchSubscriptionGateway(gateway) {
        const map = {
            bkash: { box: 'gateway-box-bkash', btn: 'tab-btn-bkash' },
            sslcommerz: { box: 'gateway-box-ssl', btn: 'tab-btn-ssl' }
        };

        Object.keys(map).forEach(function(gw) {
            const box = document.getElementById(map[gw].box);
            const btn = document.getElementById(map[gw].btn);
            if (box) box.classList.toggle('hidden', gw !== gateway);
            if (btn) btn.style.borderColor = gw === gateway ? 'var(--primary)' : '';
        });
    }

    function openSubscriptionPaymentModal(trigger) {
        if (!trigger) return;

        const action = trigger.dataset.action;
        const name = trigger.dataset.name || 'Plan';
        const price = trigger.dataset.price || '';

        const title = document.getElementById('subscription-modal-title');
        const members = document.getElementById('subscription-members');
        const users = document.getElementById('subscription-users');
        const projects = document.getElementById('subscription-projects');
        const billing = document.getElementById('subscription-billing');

        if (title) title.textContent = name + ' Subscription';
        if (members) members.textContent = trigger.dataset.members || '—';
        if (users) users.textContent = trigger.dataset.users || '—';
        if (projects) projects.textContent = trigger.dataset.projects || '—';
        if (billing) billing.textContent = trigger.dataset.billing || '—';

        document.querySelectorAll('.subscription-pay-price').forEach(function(el) {
            el.textContent = price;
        });

        const bkashForm = document.getElementById('subscription-form-bkash');
        const sslForm = document.getElementById('subscription-form-ssl');
        if (bkashForm && action) bkashForm.action = action;
        if (sslForm && action) sslForm.action = action;

        showClientModal('subscription-payment-modal');
    }

    window.showClientModal = showClientModal;
    window.hideClientModal = hideClientModal;
    window.openSubscriptionPaymentModal = openSubscriptionPaymentModal;
    window.switchSubscriptionGateway = switchSubscriptionGateway;

    document.addEventListener('click', function(event) {
        const subscribeTrigger = event.target.closest('.subscription-modal-trigger');
        if (subscribeTrigger) {
            event.preventDefault();
            openSubscriptionPaymentModal(subscribeTrigger);
            return;
        }

        const restrictionTrigger = event.target.closest('.package-restriction-trigger');
        if (restrictionTrigger) {
            event.preventDefault();
            const message = restrictionTrigger.dataset.error || 'This package is not available for your current account usage.';
            const target = document.getElementById('package-restriction-message');
            if (target) target.textContent = message;
            showClientModal('package-restriction-modal');
        }
    });
</script>
</x-client.layout.app>
