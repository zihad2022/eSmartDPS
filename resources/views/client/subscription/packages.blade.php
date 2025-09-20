<x-client.layout.app>
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'Subscription Packages', 'url' => route('client.subscription.packages')],
        ];

        // Load client with active & latest package
        $client = \App\Models\Client::with(['activeClientPackage.package', 'latestClientPackage.package'])
            ->findOrFail(owner_client_id());

        // Prefer active package, otherwise take latest
        $clientPackage = $client->activeClientPackage ?? $client->latestClientPackage;
        $activePackageId = $clientPackage?->package_id;
        $isExpired = $clientPackage?->ends_at && now()->greaterThan($clientPackage->ends_at);
    @endphp

    <x-slot:title>Subscription Packages</x-slot:title>
    <x-breadcrumb :items="$breadcrumbItems" />

        {{-- ===========================
             Flash Messages Section
        ============================ --}}
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
                        / {{ $package->billing_cycle->label() }}
                    </span>
                </div>

                {{-- Features --}}
                <ul class="mb-6 space-y-3 text-primary-700">
                    <li>✔ Members: {{ $package->member_limit ?: 'Unlimited' }}</li>
                    <li>✔ Users: {{ $package->user_limit ?: 'Unlimited' }}</li>
                    <li>✔ Projects: {{ $package->project_limit ?: 'Unlimited' }}</li>
                </ul>

                {{-- Action Button --}}
                @if (!$isCurrent)
                    {{-- Not current plan --}}
                    <form action="{{ route('client.start.subscription') }}" method="POST" class="mt-auto">
                        @csrf
                        <input type="hidden" name="package_id" value="{{$package->id}}">
                        <button type="submit"
                            class="w-full bg-accent-500 hover:bg-accent-600 text-white py-2 px-4 rounded-lg transition duration-300">
                            {{ $package->has_trial ? 'Start Trial' : 'Active Plan' }}
                        </button>
                    </form>
                @elseif ($isExpired)
                    {{-- Current but expired --}}
                    <form action="{{ route('client.subscription.renew') }}" method="GET" class="mt-auto">
                        @csrf
                        <button type="submit"
                            class="w-full bg-accent-500 hover:bg-accent-600 text-white py-2 px-4 rounded-lg transition duration-300">
                            Activate Plan
                        </button>
                    </form>
                @else
                    {{-- Current and active --}}
                    <button disabled
                        class="mt-auto w-full bg-gray-200 text-gray-600 py-2 px-4 rounded-lg cursor-not-allowed">
                        Active Plan
                    </button>
                @endif
            </div>
        @endforeach
    </div>
</x-client.layout.app>
