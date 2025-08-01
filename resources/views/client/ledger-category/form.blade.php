<x-client.layout.app>
    <div class="">
        <div class="bg-white rounded-2xl shadow-sm p-8">
            @php $editing = isset($category); @endphp

            <h2 class="text-2xl font-bold text-primary-900 mb-6">
                {{ $editing ? 'Edit Ledger Category' : 'Add Ledger Category' }}
            </h2>

            <form method="POST"
                action="{{ $editing ? route('client.ledger-categories.update', $category->id) : route('client.ledger-categories.store') }}"
                class="space-y-8">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                {{-- Category Name --}}
                <x-form.input name="name" label="Category Name" :value="old('name', $category->name ?? '')" required
                    placeholder="Enter category name" />

                {{-- Type (Income/Expense) --}}
                <div>
                    <x-form.label for="type">Type</x-form.label>
                    <select name="type" id="type"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-accent-500">
                        <option value="income" {{ old('type', $category->type ?? '') === 'income' ? 'selected' : '' }}>
                            Income</option>
                        <option value="expense"
                            {{ old('type', $category->type ?? '') === 'expense' ? 'selected' : '' }}>Expense</option>
                    </select>
                    @error('type')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <x-form.label for="description">Description</x-form.label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-accent-500">{{ old('description', $category->description ?? '') }}</textarea>
                    @error('description')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <x-form.label for="is_active">Status</x-form.label>
                    <select name="is_active" id="is_active"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-accent-500">
                        <option value="1" {{ old('is_active', $category->is_active ?? 1) == 1 ? 'selected' : '' }}>
                            Active</option>
                        <option value="0" {{ old('is_active', $category->is_active ?? 1) == 0 ? 'selected' : '' }}>
                            Inactive</option>
                    </select>
                    @error('is_active')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('client.ledger-categories.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition">
                        {{ $editing ? 'Update Category' : 'Add Category' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-client.layout.app>
