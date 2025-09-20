@php
    use App\Enums\Package\BillingCycle;
@endphp
<div class="relative bg-white rounded-2xl shadow-lg p-8 border border-gray-200 hover:shadow-xl transition duration-300">
    <!-- Popular badge (if trial available for example) -->
    @if($package->has_trial)
        <span class="absolute top-4 right-4 bg-accent-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
            Trial {{ $package->trial_days }} Days
        </span>
    @endif

    <!-- Package name -->
    <h3 class="text-2xl font-bold text-primary-900 mb-3">{{ $package->name }}</h3>
    <p class="text-primary-600 mb-6">{{ $package->description }}</p>

    <!-- Pricing -->
    <div class="mb-6">
        @php
            $finalPrice = $package->price;
            if ($package->discount_type && $package->discount_value > 0) {
                if ($package->discount_type == 1) { // % discount
                    $finalPrice = $package->price - ($package->price * $package->discount_value / 100);
                } else { // flat discount
                    $finalPrice = $package->price - $package->discount_value;
                }
            }
        @endphp

        <div class="flex items-end space-x-2">
            <span class="text-4xl font-extrabold text-accent-600">
                ${{ number_format($finalPrice, 2) }}
            </span>
            @if($package->discount_value > 0)
                <span class="line-through text-gray-400">${{ number_format($package->price, 2) }}</span>
            @endif
        </div>
        <p class="text-sm text-gray-500 mt-1">
            @if($package->billing_cycle == BillingCycle::MONTHLY)
                per month
            @elseif($package->billing_cycle == BillingCycle::YEARLY)
                per year
            @else
                Contact Us
            @endif
        </p>
    </div>

    <!-- Features -->
    <ul class="space-y-3 text-sm text-primary-600 mb-8">
        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Up to {{ $package->member_limit ?: '∞' }} Members</li>
        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Up to {{ $package->user_limit ?: '∞' }} Users</li>
        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Up to {{ $package->project_limit ?: '∞' }} Projects</li>
    </ul>

    <!-- Action -->
    {{-- {{ route('packages.subscribe', $package->id) }} --}}
    <a href="{{route('client.register', ['package' => $package->id])}}"
        class="block text-center px-6 py-3 rounded-xl text-white bg-accent-500 hover:bg-accent-600 font-medium transition duration-300">
        Choose Plan
    </a>
</div>