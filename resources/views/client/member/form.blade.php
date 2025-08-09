<x-client.layout.app>
    <div class="">
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $editing = isset($member) ? 'Edit Member' : 'Add New Member' }}
            </h2>

            @php
                $editing = isset($member);
            @endphp

            {{-- <form method="POST"
                action="{{ $editing ? route('client.members.update', $member->id) : route('client.members.store') }}"
                class="space-y-6">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="member_id" label="Member ID" :value="$memberId ?? $member->member_id" required
                        placeholder="Enter member ID" :disabled="true" />
                    <x-form.input name="name" label="Full Name" :value="old('name', $member->name ?? '')" required
                        placeholder="Enter full name" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="email" label="Email" type="email" :value="old('email', $member->email ?? '')"
                        placeholder="Enter email address" required />
                    <x-form.input name="phone" label="Phone" type="tel" :value="old('phone', $member->phone ?? '')"
                        placeholder="Enter phone number" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-form.input name="password" label="Password" type="password" placeholder="Enter password"
                            :required="!$editing" />
                        @if ($editing)
                            <p class="text-xs text-gray-500 mt-1">Leave blank to keep existing password.</p>
                        @endif
                    </div>
                    <x-form.select name="status" label="Status" :options="['1' => 'Active', '0' => 'Inactive']" :selected="old('status', $member->status ?? '1')" />
                </div>

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
            </form> --}}
            <form method="POST"
                action="{{ $editing ? route('client.members.update', $member->id) : route('client.members.store') }}"
                class="space-y-8">
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
                </div>

                <!-- 🟡 SHARE PURCHASE INFORMATION -->
                {{-- <div class="bg-gray-50 p-4 rounded-lg border">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4">Share Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form.select name="share_id" label="Select Share Plan" :options="$shares->pluck('name', 'id')" :selected="old('share_id', $memberShare->share_id ?? '')"
                            required :isOptionLabel="true" optionLabel="Select Share Plan" />
                        <x-form.input name="shares_count" label="Number of Shares" type="number" min="1"
                            :value="old('shares_count', $memberShare->shares_count ?? 1)" required />
                    </div>
                </div> --}}


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
