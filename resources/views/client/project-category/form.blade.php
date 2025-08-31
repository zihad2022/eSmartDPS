<div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">

    {{-- Page Title (changes based on Add/Edit mode) --}}
    <h2 class="text-xl font-semibold text-primary-900 mb-6">
        {{ isset($editCategory) ? 'Edit Category' : 'Add New Category' }}
    </h2>

    {{-- Form Start (Dynamic Action: Store or Update Category) --}}
    <form method="POST"
        action="{{ isset($editCategory) 
                    ? route('client.project-categories.update', $editCategory) 
                    : route('client.project-categories.store') }}"
        class="space-y-6">
        
        {{-- CSRF Protection --}}
        @csrf

        {{-- Use PUT method only when editing --}}
        @if (isset($editCategory))
            @method('PUT')
        @endif

        {{-- Category Name Input Field --}}
        <div>
            <x-form.input 
                name="name" 
                label="Category Name" 
                :value="old('name', $editCategory->name ?? '')" 
                required
                placeholder="Enter category name" />
        </div>

        {{-- Action Buttons (Cancel + Submit) --}}
        <div class="flex justify-end space-x-4 pt-4">

            {{-- Show Cancel button only on Edit --}}
            @if (isset($editCategory))
                <a href="{{ route('client.project-categories.index') }}"
                    class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                    Cancel
                </a>
            @endif

            {{-- Submit Button (Dynamic: Add or Update) --}}
            <button type="submit"
                class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                {{ isset($editCategory) ? 'Update Category' : 'Add Category' }}
            </button>
        </div>
    </form>
    {{-- Form End --}}
</div>
