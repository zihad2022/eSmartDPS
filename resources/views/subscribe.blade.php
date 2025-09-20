<x-app-layout>
    <section class="py-20 bg-gray-50">
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-3xl font-bold text-primary-900 mb-6">Subscribe to {{ $package->name }}</h2>
    
            <p class="text-primary-600 mb-6">{{ $package->description }}</p>
    
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
                    @if($package->billing_cycle == 1)
                        per month
                    @elseif($package->billing_cycle == 2)
                        per year
                    @else
                        one-time
                    @endif
                </p>
            </div>
    
            <!-- Features -->
            <ul class="space-y-3 text-sm text-primary-600 mb-8">
                <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Up to {{ $package->member_limit ?: '∞' }} Members</li>
                <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Up to {{ $package->user_limit ?: '∞' }} Users</li>
                <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Up to {{ $package->project_limit ?: '∞' }} Projects</li>
            </ul>
    
            <!-- Confirm button -->
            <form method="POST" action="{{ route('packages.processSubscribe', $package->id) }}">
                @csrf
                <button type="submit"
                    class="w-full px-6 py-3 rounded-xl text-white bg-accent-500 hover:bg-accent-600 font-medium transition duration-300">
                    Confirm Subscription
                </button>
            </form>
        </div>
    </section>
</x-app-layout>
