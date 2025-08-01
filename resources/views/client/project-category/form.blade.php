<x-client.layout.app>
    <div class="">
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ isset($category) ? 'Edit Project Category' : 'Add New Project Category' }}
            </h2>

            <form method="POST"
                action="{{ isset($category) ? route('client.project-categories.update', $category) : route('client.project-categories.store') }}"
                class="space-y-6">
                @csrf
                @if (isset($category))
                    @method('PUT')
                @endif

                <div>
                    <x-form.input name="name" label="Category Name" :value="old('name', $category->name ?? '')" required
                        placeholder="Enter category name" />
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('client.project-categories.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ isset($category) ? 'Update Category' : 'Add Category' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-client.layout.app>
