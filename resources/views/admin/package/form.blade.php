<x-admin.layout.app>
    @php
        // Check if we are editing an existing package or creating a new one
        $editing = isset($package);
    @endphp

    {{-- 🔹 Breadcrumb Navigation --}}
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'All Packages', 'url' => route('admin.packages.index')],
        ['label' => $editing ? 'Edit Package' : 'Add New Package'],
    ]" />

    {{-- 🔹 Dynamic Page Title --}}
    @if ($editing)
        <x-slot:title>Edit Package</x-slot:title>
    @else
        <x-slot:title>Add New Package</x-slot:title>
    @endif

    <div>
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            {{-- 🔹 Heading --}}
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $editing ? 'Edit Package' : 'Add New Package' }}
            </h2>

            {{-- 🔹 Form to Add / Update Package --}}
            <form method="POST"
                action="{{ $editing ? route('admin.packages.update', $package->id) : route('admin.packages.store') }}"
                class="space-y-8">
                @csrf
                @if ($editing)
                    {{-- Use PUT method when updating --}}
                    @method('PUT')
                @endif

                {{-- 🔹 Basic Info Section --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Package Name Input --}}
                    <x-form.input name="name" label="Package Name" required :value="old('name', $package->name ?? '')"
                        placeholder="e.g. Basic, Pro, Enterprise" />

                    {{-- Billing Cycle Dropdown (from Enum) --}}
                    <x-form.select name="billing_cycle" label="Billing Cycle" :options="collect(\App\Enums\Package\BillingCycle::cases())
                        ->mapWithKeys(fn($type) => [$type->value => $type->label()])
                        ->toArray()" :selected="old('billing_cycle', $package->billing_cycle?->value ?? '')" />
                </div>

                {{-- 🔹 Description Field --}}
                <div>
                    <x-form.textarea name="description" label="Description" :value="old('description', $package->description ?? '')"
                        placeholder="Describe this plan..." />
                    @error('description')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 🔹 Pricing Section --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Package Price --}}
                    <x-form.input name="price" label="Price" type="number" step="0.01" min="0" required
                        :value="old('price', $package->price ?? '')" placeholder="Enter price" />

                    {{-- Discount Value --}}
                    <x-form.input name="discount_value" label="Discount Value" type="number" step="0.01"
                        min="0" :value="old('discount_value', $package->discount_value ?? '')"
                        placeholder="Enter discount value (leave blank for no discount)" />

                    {{-- Discount Type Dropdown (Percent / Fixed) --}}
                    <x-form.select name="discount_type" label="Discount Type" :options="collect(\App\Enums\Package\DiscountType::cases())
                        ->mapWithKeys(fn($type) => [$type->value => $type->label()])
                        ->toArray()" :selected="old('discount_type', $package->discount_type?->value ?? '')" />
                </div>

                {{-- 🔹 Limits Section --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Member Limit --}}
                    <x-form.input name="member_limit" label="Member Limit" type="number" min="0"
                        :value="old('member_limit', $package->member_limit ?? 0)" placeholder="0 = Unlimited" />

                    {{-- User Limit --}}
                    <x-form.input name="user_limit" label="User Limit" type="number" min="0" :value="old('user_limit', $package->user_limit ?? 0)"
                        placeholder="0 = Unlimited" />

                    {{-- Project Limit --}}
                    <x-form.input name="project_limit" label="Project Limit" type="number" min="0"
                        :value="old('project_limit', $package->project_limit ?? 0)" placeholder="Optional" />
                </div>

                {{-- 🔹 Free Trial Section --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Free Trial Option --}}
                    <x-form.select name="has_trial" label="Has Free Trial?" :options="['1' => 'Yes', '0' => 'No']" :selected="old('has_trial', $package->has_trial ?? '1')" />

                    {{-- Free Trial Days --}}
                    <x-form.input name="trial_days" label="Trial Days" type="number" min="0" :value="old('trial_days', $package->trial_days ?? 0)"
                        placeholder="e.g. 7" />
                </div>

                {{-- 🔹 Status Dropdown (Active / Inactive) --}}
                <x-form.select name="is_active" label="Status" :options="['1' => 'Active', '0' => 'Inactive']" :selected="old('is_active', $package->is_active ?? '1')" />

                {{-- 🔹 Action Buttons --}}
                <div class="flex justify-end space-x-4 pt-4">
                    {{-- Cancel Button --}}
                    <a href="{{ route('admin.packages.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                        Cancel
                    </a>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ $editing ? 'Update Package' : 'Add Package' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin.layout.app>
