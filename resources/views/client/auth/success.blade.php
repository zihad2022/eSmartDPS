<x-app-layout>
    <x-slot:title>Registration Successful</x-slot:title>
    <section
        class="min-h-screen flex items-center justify-center bg-gradient-to-b from-accent-50 to-indigo-50 py-20 px-4">
        <div class="max-w-4xl w-full bg-white rounded-3xl shadow-2xl p-10 md:p-14 text-center relative overflow-hidden">

            <!-- Decorative Gradient Circle -->
            <div
                class="absolute -top-16 -right-16 w-40 h-40 bg-gradient-to-tr from-accent-300 to-accent-400 rounded-full opacity-30">
            </div>

            <!-- Success Icon -->
            <div class="flex justify-center mb-6">
                <div
                    class="w-24 h-24 flex items-center justify-center rounded-full bg-gradient-to-br from-accent-100 to-accent-100 shadow-inner">
                    <i class="fas fa-check-circle text-accent-500 text-6xl"></i>
                </div>
            </div>

            <!-- Success Message -->
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3">
                Registration Successful 🎉
            </h1>
            <p class="text-gray-700 text-lg md:text-xl mb-8">
                Your account has been created successfully. Welcome aboard! Get started with your subscription below.
            </p>

            <!-- Order Summary Card -->
            <div
                class="bg-gradient-to-r from-accent-50 to-accent-50 border border-accent-100 rounded-2xl p-6 mb-8 shadow-sm text-left">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-receipt text-accent-500 mr-2"></i> Order Summary
                </h3>
                <dl class="space-y-3 text-sm text-gray-700">
                    <!-- Plan Name -->
                    <div class="flex justify-between">
                        <dt class="font-medium">Plan</dt>
                        <dd class="font-semibold">{{ $package->name }}</dd>
                    </div>

                    <!-- Billing Cycle -->
                    <div class="flex justify-between">
                        <dt class="font-medium">Billing Cycle</dt>
                        <dd class="font-semibold">
                            @if ($package->billing_cycle == App\Enums\Package\BillingCycle::MONTHLY)
                                Monthly
                            @elseif ($package->billing_cycle == App\Enums\Package\BillingCycle::YEARLY)
                                Yearly
                            @else
                                Custom
                            @endif
                        </dd>
                    </div>

                    <!-- Original Price -->
                    <div class="flex justify-between">
                        <dt class="font-medium">Price</dt>
                        <dd class="font-semibold">{{ number_format($package->price, 2) }} USD</dd>
                    </div>


                    <!-- Discount -->
                    @if ($package->discount_value > 0)
                        <div class="flex justify-between text-green-600">
                            <dt class="font-medium">Discount</dt>
                            <dd class="font-semibold">
                                -
                                @if ($package->discount_type === \App\Enums\Package\DiscountType::FIXED)
                                    {{ number_format($package->discount_amount, 2) }} USD
                                @elseif ($package->discount_type === \App\Enums\Package\DiscountType::PERCENT)
                                    {{ $package->discount_value }}%
                                    ({{ number_format($package->discount_amount, 2) }} USD)
                                @endif
                            </dd>
                        </div>
                    @endif

                    <!-- Total After Discount -->
                    <div class="flex justify-between font-bold text-gray-900 border-t pt-3 mt-2">
                        <dt>Total</dt>
                        <dd>{{ number_format($package->total_after_discount, 2) }} USD</dd>
                    </div>



                    <!-- Free Trial (if applicable) -->
                    @if ($package->has_trial && $package->trial_days > 0)
                        <div class="mt-2 text-sm text-blue-600 font-medium">
                            Free Trial: {{ $package->trial_days }} days
                        </div>
                    @endif
                </dl>
            </div>


            <!-- Plan Features -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-8 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Plan Features</h3>
                <ul class="space-y-3 text-gray-700 text-sm md:text-base">
                    @if ($package->member_limit > 0)
                        <li class="flex items-center">
                            <i class="fas fa-users text-accent-500 mr-3 text-sm md:text-base"></i> Up to
                            {{ $package->member_limit }} members
                        </li>
                    @endif
                    @if ($package->user_limit > 0)
                        <li class="flex items-center">
                            <i class="fas fa-user text-accent-500 mr-3 text-sm md:text-base"></i> Up to
                            {{ $package->user_limit }} users
                        </li>
                    @endif
                    @if ($package->project_limit > 0)
                        <li class="flex items-center">
                            <i class="fas fa-folder-open text-accent-500 mr-3 text-sm md:text-base"></i> Manage up to
                            {{ $package->project_limit }} projects
                        </li>
                    @endif
                    @if ($package->has_trial)
                        <li class="flex items-center">
                            <i class="fas fa-clock text-accent-500 mr-3 text-sm md:text-base"></i> Free trial for
                            {{ $package->trial_days }} days
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Actions -->
            <div class="flex flex-col md:flex-row gap-4">
                @if ($package->has_trial)
                    <a href="{{ route('client.dashboard') }}"
                        class="flex-1 py-3 px-6 bg-accent-500 text-white rounded-xl font-semibold hover:bg-accent-600 transition flex items-center justify-center">
                        <i class="fas fa-play mr-2"></i> Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('payment.checkout', $package->id) }}"
                        class="flex-1 py-3 px-6 bg-accent-600 text-white rounded-xl font-semibold hover:bg-accent-700 transition flex items-center justify-center">
                        <i class="fas fa-lock mr-2"></i> Complete Payment
                    </a>
                @endif

                <a href="{{ route('home') }}"
                    class="flex-1 py-3 px-6 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-100 transition flex items-center justify-center">
                    <i class="fas fa-home mr-2"></i> Back to Home
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
