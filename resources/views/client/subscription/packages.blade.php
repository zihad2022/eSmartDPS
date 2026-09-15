<x-client.layout.app>
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'Subscription Packages', 'url' => route('client.subscription.packages')],
        ];

        $client = \App\Models\Client::with(['activeClientPackage.package', 'latestClientPackage.package'])
            ->findOrFail(owner_client_id());

        $clientPackage = $client->activeClientPackage ?? $client->latestClientPackage;
        $activePackageId = $clientPackage?->package_id;
        $isExpired = $clientPackage?->ends_at && now()->greaterThan($clientPackage->ends_at);
    @endphp

    <x-slot:title>Subscription Packages</x-slot:title>
    <x-breadcrumb :items="$breadcrumbItems" />

    {{-- Flash Messages --}}
    @if (session('success') || session('error'))
        <x-flash-message
            :type="session('success') ? 'success' : 'error'"
            :title="session('success') ? 'Success' : 'Error'"
            :message="session('success') ?? session('error')"
        />
    @endif

    <div class="grid gap-8 md:grid-cols-3">
        @foreach ($packages as $package)
            @php
                $isCurrent = $activePackageId === $package->id;

                // Discount calculation
                $discountedPrice = $package->price;
                if ($package->discount_value > 0) {
                    $discountedPrice = $package->discount_type === \App\Enums\Package\DiscountType::FIXED
                        ? $package->price - $package->discount_value
                        : $package->price - ($package->price * $package->discount_value / 100);

                    $discountedPrice = max(0, $discountedPrice);
                }
            @endphp

            <div
                @class([
                    'relative bg-white rounded-2xl shadow transition duration-300 p-6 flex flex-col',
                    'hover:shadow-lg' => !$isCurrent,
                    'border-2 border-green-500 shadow-lg' => $isCurrent,
                ])>

                {{-- Current Plan Badge --}}
                @if ($isCurrent)
                    <span
                        class="absolute top-4 right-4 bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                        Your Current Plan
                    </span>
                @endif

                {{-- Package Name --}}
                <h3 class="text-xl font-semibold text-primary-900 mb-4">
                    {{ $package->name }}
                    @if ($package->has_trial)
                        <span class="ml-2 text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">
                            Trial Available ({{ $package->trial_days }} days)
                        </span>
                    @endif
                </h3>

                {{-- Description --}}
                <p class="text-primary-700 mb-6">{{ $package->description }}</p>

                {{-- Price --}}
                <div class="text-4xl font-bold text-primary-900 mb-4">
                    @if ($package->discount_value > 0)
                        <span class="line-through text-lg text-primary-400">
                            {{ $settings->currency . ' ' . number_format($package->price) }}
                        </span>
                        {{ $settings->currency . ' ' . number_format($discountedPrice) }}
                    @else
                        {{ $settings->currency . ' ' . number_format($package->price) }}
                    @endif
                    <span class="text-base font-medium text-primary-500">
                        / {{ $package->billing_cycle?->label() ?? 'N/A' }}
                    </span>
                </div>

                {{-- Features --}}
                <ul class="mb-6 space-y-3 text-primary-700">
                    <li>✔ Members: {{ $package->member_limit ?: 'Unlimited' }}</li>
                    <li>✔ Users: {{ $package->user_limit ?: 'Unlimited' }}</li>
                    <li>✔ Projects: {{ $package->project_limit ?: 'Unlimited' }}</li>
                </ul>

                {{-- Action Buttons --}}
                <div class="mt-auto pt-4 space-y-2">
                    @if (!$isCurrent)
                        {{-- Direct Buy with bKash / Payment Gateway --}}
                        <form action="{{ route('client.subscription.start.paid', $package) }}" method="GET">
                            <input type="hidden" name="package_id" value="{{ $package->id }}">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-md shadow-pink-500/20 transition duration-200 text-sm">
                                <i class="fas fa-shopping-cart text-xs"></i>
                                <span>Buy with bKash</span>
                            </button>
                        </form>

                        {{-- Optional Free Trial Option --}}
                        @if ($package->has_trial)
                            <form action="{{ route('client.subscription.start.trial', $package) }}" method="GET">
                                <input type="hidden" name="package_id" value="{{ $package->id }}">
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium py-2 px-4 rounded-xl transition duration-200 text-xs border border-slate-200">
                                    <i class="far fa-clock text-xs text-slate-500"></i>
                                    <span>Start {{ $package->trial_days }}-Day Free Trial</span>
                                </button>
                            </form>
                        @endif

                    @elseif ($isExpired)
                        {{-- Current but expired - Renew Plan --}}
                        <form action="{{ route('client.subscription.start.paid', $package) }}" method="GET">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-md shadow-pink-500/20 transition duration-200 text-sm">
                                <i class="fas fa-redo-alt text-xs"></i>
                                <span>Renew with bKash</span>
                            </button>
                        </form>
                    @else
                        {{-- Current and active --}}
                        <button disabled
                            class="w-full inline-flex items-center justify-center gap-2 bg-emerald-50 text-emerald-700 font-semibold py-2.5 px-4 rounded-xl border border-emerald-200/80 cursor-default text-sm">
                            <i class="fas fa-check-circle text-emerald-500"></i>
                            <span>Currently Active</span>
                        </button>
                    @endif

                    {{-- bKash Instant Badge --}}
                    <div class="flex items-center justify-center gap-1.5 text-[11px] text-slate-400 pt-1">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Instant bKash Tokenized Checkout</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-client.layout.app>
