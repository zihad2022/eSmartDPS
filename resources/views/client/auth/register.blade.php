<x-app-layout>
    <x-slot:title>Register</x-slot:title>
    <section class="py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Order Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <form id="registrationForm" action="{{ route('client.register.store') }}" method="POST"
                            class="space-y-8">
                            @csrf
                            <input type="hidden" name="package_id" value="{{ $package->id }}">
                            <!-- Organization Details -->
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                    <i class="fas fa-building text-accent-500 mr-3"></i>
                                    Organization Details
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-form.input name="organization_name" label="Organization Name" required
                                        placeholder="Organization Name" />
                                    <x-form.input name="short_name" label="Short Name" required
                                        placeholder="Short Name" />
                                    <x-form.input name="contact_email" label="Contact Email" required
                                        placeholder="Contact Email" />
                                    <x-form.input name="contact_phone" label="Contact Phone" required
                                        placeholder="Contact Phone" />
                                </div>
                            </div>
                            <!-- Admin Account Details -->
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                    <i class="fas fa-user-shield text-accent-500 mr-3"></i>
                                    Admin Account Details
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-form.input name="first_name" label="First Name" required
                                        placeholder="First Name" />
                                    <x-form.input name="last_name" label="Last Name" required placeholder="Last Name" />
                                    <!-- Email full width -->
                                    <div class="md:col-span-2">
                                        <x-form.input name="email" label="Email" required placeholder="Email" />
                                    </div>
                                    <x-form.input name="phone" label="Phone" required placeholder="Phone" />
                                    <x-form.input name="password" label="Password" type="password" required
                                        placeholder="Password" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Order Summary</h3>
                        <div class="bg-gray-50 rounded-xl p-4 mb-6">
                            <div class="font-semibold text-gray-900 mb-2" id="selectedPlan">{{ $package->name }}</div>
                            <div class="text-sm text-gray-600 mb-4">{{ $package->description }}</div>
                            <ul class="space-y-2 text-sm text-gray-600">
                                @if ($package->member_limit > 0)
                                    <li class="flex items-center">
                                        <i class="fas fa-users text-accent-500 mr-2 text-xs"></i>
                                        Up to {{ $package->member_limit }} members
                                    </li>
                                @endif
                                @if ($package->user_limit > 0)
                                    <li class="flex items-center">
                                        <i class="fas fa-user text-accent-500 mr-2 text-xs"></i>
                                        Up to {{ $package->user_limit }} users
                                    </li>
                                @endif
                                @if ($package->project_limit > 0)
                                    <li class="flex items-center">
                                        <i class="fas fa-folder-open text-accent-500 mr-2 text-xs"></i>
                                        Manage up to {{ $package->project_limit }} projects
                                    </li>
                                @endif
                                @if ($package->has_trial)
                                    <li class="flex items-center">
                                        <i class="fas fa-clock text-accent-500 mr-2 text-xs"></i>
                                        Free trial for {{ $package->trial_days }} days
                                    </li>
                                @endif
                                <li class="flex items-center">
                                    <i class="fas fa-shield-alt text-accent-500 mr-2 text-xs"></i>
                                    Secure & encrypted payments
                                </li>
                            </ul>
                        </div>
                        <div class="space-y-3 mb-6">
                            <div class="border-t pt-3">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>{{ $package->billing_cycle->label() }} Subscription</span>
                                    <span id="totalAmount">{{ $settings->currency }}
                                        {{ number_format($package->price) }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- Submit Button (Now submits the form) -->
                        <button type="submit" form="registrationForm"
                            class="w-full bg-accent-500 text-white py-3 px-6 rounded-lg font-semibold mb-4 hover:bg-accent-600 transition">
                            @if ($package->has_trial)
                                <i class="fas fa-play mr-2"></i>
                                Start Free Trial
                            @else
                                <i class="fas fa-lock mr-2"></i>
                                Complete Payment
                            @endif
                        </button>
                        <div class="flex items-center justify-center text-sm text-gray-500">
                            <i class="fas fa-shield-alt text-accent-500 mr-2"></i>
                            <span>Your payment information is secure and encrypted</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
