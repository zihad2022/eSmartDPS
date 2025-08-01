<x-admin.layout.app>
    @php
        $editing = isset($pakage);
    @endphp
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'All Pakages', 'url' => route('admin.pakages.index')],
        ['label' => $editing ? 'Edit Pakage' : 'Add New Pakage'],
    ]" />
    @if ($editing)
        <x-slot:title>Edit Pakage</x-slot:title>
    @else
        <x-slot:title>Add New Pakage</x-slot:title>
    @endif
    <div>
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $editing ? 'Edit Pakage' : 'Add New Pakage' }}
            </h2>

            <form method="POST"
                action="{{ $editing ? route('admin.pakages.update', $pakage->id) : route('admin.pakages.store') }}"
                class="space-y-8">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                {{-- 📛 Basic Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="name" label="Pakage Name" required :value="old('name', $pakage->name ?? '')"
                        placeholder="e.g. Basic, Pro, Enterprise" />

                    <x-form.select name="billing_cycle" label="Billing Cycle" :options="['monthly' => 'Monthly', 'yearly' => 'Yearly']" :selected="old('billing_cycle', $pakage->billing_cycle ?? '')"
                        required />
                </div>

                {{-- 📝 Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-primary-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4"
                        class="w-full border border-gray-300 rounded-lg text-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-accent-500"
                        placeholder="Describe this plan...">{{ old('description', $pakage->description ?? '') }}</textarea>
                    @error('description')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 💵 Pricing --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-form.input name="price" label="Price" type="number" step="0.01" min="0" required
                        :value="old('price', $pakage->price ?? '')" placeholder="Enter price" />

                    <x-form.input name="discount_value" label="Discount Value" type="number" step="0.01"
                        min="0" :value="old('discount_value', $pakage->discount_value ?? '')" placeholder="Enter discount value" />

                    <x-form.select name="discount_type" label="Discount Type" :options="['percentage' => 'Percentage (%)', 'amount' => 'Fixed Amount']" :selected="old('discount_type', $pakage->discount_type ?? '')" />

                </div>

                {{-- 📊 Limits --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-form.input name="member_limit" label="Member Limit" type="number" min="0"
                        :value="old('member_limit', $pakage->member_limit ?? 0)" placeholder="0 = Unlimited" />

                    <x-form.input name="user_limit" label="User Limit" type="number" min="0" :value="old('user_limit', $pakage->user_limit ?? 0)"
                        placeholder="0 = Unlimited" />

                    <x-form.input name="project_limit" label="Project Limit" type="number" min="0"
                        :value="old('project_limit', $pakage->project_limit ?? 0)" placeholder="Optional" />
                </div>

                {{-- 🎁 Free Trial --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.select name="has_trial" label="Has Free Trial?" :options="['1' => 'Yes', '0' => 'No']" :selected="old('has_trial', $pakage->has_trial ?? '1')" />

                    <x-form.input name="trial_days" label="Trial Days" type="number" min="0" :value="old('trial_days', $pakage->trial_days ?? 0)"
                        placeholder="e.g. 7" />
                </div>

                {{-- ✅ Status --}}
                <x-form.select name="is_active" label="Status" :options="['1' => 'Active', '0' => 'Inactive']" :selected="old('is_active', $pakage->is_active ?? '1')" />

                {{-- 🔘 Submit --}}
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('admin.pakages.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ $editing ? 'Update Pakage' : 'Add Pakage' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin.layout.app>
