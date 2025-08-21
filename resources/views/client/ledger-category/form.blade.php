<div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
    <h2 class="text-xl font-semibold text-primary-900 mb-6">
        {{ isset($ledgerCategory) ? 'Edit Ledger Category' : 'Add New Ledger Category' }}
    </h2>

    <form method="POST"
        action="{{ isset($ledgerCategory) ? route('client.ledger-categories.update', $ledgerCategory) : route('client.ledger-categories.store') }}"
        class="space-y-6">
        @csrf
        @if (isset($ledgerCategory))
            @method('PUT')
        @endif

        <!-- Category Name -->
        <div>
            <x-form.input name="name" label="Category Name" :value="old('name', $ledgerCategory->name ?? '')" required
                placeholder="Enter category name" />
        </div>

        <!-- Description -->
        <div>
            <x-form.textarea name="description" label="Description" :value="old('description', $ledgerCategory->description ?? '')"
                placeholder="Enter category description (optional)" />
        </div>
        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 pt-4">
            @if (isset($ledgerCategory))
                <a href="{{ route('client.ledger-categories.index') }}"
                    class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                    Cancel
                </a>
            @endif
            <button type="submit"
                class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                {{ isset($ledgerCategory) ? 'Update Category' : 'Add Category' }}
            </button>
        </div>
    </form>
</div>
