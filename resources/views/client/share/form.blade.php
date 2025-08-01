<x-client.layout.app>
    <div class="">
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $editing = isset($share) ? 'Edit Share' : 'Add New Share' }}
            </h2>

            @php
                $editing = isset($share);
            @endphp

            <form method="POST"
                action="{{ $editing ? route('client.shares.update', $share->id) : route('client.shares.store') }}"
                class="space-y-6">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                {{-- Name & Price --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="name" label="Share Name" :value="old('name', $share->name ?? '')" required
                        placeholder="Enter share name" />

                    <x-form.input name="price" label="Price" type="number" step="0.01" :value="old('price', $share->price ?? '')" required
                        placeholder="Enter price" />
                </div>

                {{-- Description --}}
                <div>
                    <x-form.label for="description">Description</x-form.label>
                    <textarea name="description" id="description" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-accent-500">{{ old('description', $share->description ?? '') }}</textarea>
                </div>

                {{-- Status --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.select name="is_active" label="Status" :options="['1' => 'Active', '0' => 'Inactive']" :selected="old('is_active', $share->is_active ?? '1')" />
                </div>

                {{-- Actions --}}
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('client.shares.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ $editing ? 'Update Share' : 'Add Share' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-client.layout.app>
