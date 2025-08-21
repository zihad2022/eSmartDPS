<x-client.layout.app>
    <div class="">
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $editing = isset($member) ? 'Edit Member' : 'Add New Member' }}
            </h2>

            @php
                $editing = isset($member);
            @endphp

            <form method="POST"
                action="{{ $editing ? route('client.members.update', $member->id) : route('client.members.store') }}"
                class="space-y-8" enctype="multipart/form-data">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                <!-- 🟢 MEMBER INFORMATION -->
                <div class="bg-gray-50 p-4 rounded-lg border">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4">Member Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form.input name="member_id" label="Member ID" :value="$memberId ?? $member->member_id" required
                            placeholder="Enter member ID" :disabled="true" />

                        <x-form.input name="name" label="Full Name" :value="old('name', $member->name ?? '')" required
                            placeholder="Enter full name" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <x-form.input name="email" label="Email" type="email" :value="old('email', $member->email ?? '')"
                            placeholder="Enter email address" required />

                        <x-form.input name="phone" label="Phone" type="tel" :value="old('phone', $member->phone ?? '')"
                            placeholder="Enter phone number" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <x-form.input name="password" label="Password" type="password" placeholder="Enter password"
                                :required="!$editing" />
                            @if ($editing)
                                <p class="text-xs text-gray-500 mt-1">Leave blank to keep existing password.</p>
                            @endif
                        </div>

                        <x-form.select name="status" label="Status" :options="['1' => 'Active', '0' => 'Inactive']" :selected="old('status', $member->status ?? '1')" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <!-- 🆕 Share Quantity -->
                        <x-form.input name="share_quantity" label="Share Quantity" type="number" min="0"
                            :value="old('share_quantity', $member->share_quantity ?? 0)" placeholder="Enter share quantity" />

                        <x-form.input name="profile_photo" label="Profile Photo" type="file" />
                    </div>
                </div>

                <!-- 🔵 ACTION BUTTONS -->
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('client.members.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ $editing ? 'Update Member' : 'Add Member' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-client.layout.app>
