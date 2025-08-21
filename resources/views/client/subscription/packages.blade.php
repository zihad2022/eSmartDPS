<x-client.layout.app>
    <div class="grid gap-8 md:grid-cols-3">
        @foreach ($packages as $package)
            @php
                $client = auth()->guard('client')->user();
                $isCurrent = $client->activeClientPackage && $client->activeClientPackage->package_id === $package->id;
            @endphp

            <div
                class="relative bg-white rounded-2xl shadow hover:shadow-lg transition duration-300 p-6 flex flex-col
            {{ $isCurrent ? 'border-2 border-green-500 shadow-lg' : '' }}">

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
                    @php
                        $discountedPrice = $package->price;

                        if ($package->discount_value > 0) {
                            $discountedPrice =
                                $package->discount_type === \App\Enums\Package\DiscountType::FIXED
                                    ? $package->price - $package->discount_value
                                    : $package->price - $package->price * ($package->discount_value / 100);

                            // Ensure no negative price
                            $discountedPrice = max(0, $discountedPrice);
                        }
                    @endphp

                    @if ($package->discount_value > 0)
                        <span class="line-through text-lg text-primary-400">
                            ${{ number_format($package->price) }}
                        </span>
                        ${{ number_format($discountedPrice) }}
                    @else
                        ${{ number_format($package->price) }}
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
                    <form action="" method="POST" class="mt-auto">
                        @csrf
                        <button type="submit"
                            class="w-full bg-accent-500 hover:bg-accent-600 text-white py-2 px-4 rounded-lg transition duration-300">
                            {{ $package->has_trial ? 'Start Trial' : 'Choose Plan' }}
                        </button>
                    </form>
                @else
                    <button disabled
                        class="mt-auto w-full bg-gray-200 text-gray-600 py-2 px-4 rounded-lg cursor-not-allowed">
                        Active Plan
                    </button>
                @endif
            </div>
        @endforeach
    </div>
</x-client.layout.app>
