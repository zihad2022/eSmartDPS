<x-client.layout.app>
    <div class="grid grid-cols-1 gap-8">

        {{-- 🔼 FORM SECTION --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ isset($editCategory) ? 'Edit Category' : 'Add New Category' }}
            </h2>

            <form method="POST"
                action="{{ isset($editCategory) ? route('client.project-categories.update', $editCategory) : route('client.project-categories.store') }}"
                class="space-y-6">
                @csrf
                @if (isset($editCategory))
                    @method('PUT')
                @endif

                <div>
                    <x-form.input name="name" label="Category Name" :value="old('name', $editCategory->name ?? '')" required
                        placeholder="Enter category name" />
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    @if (isset($editCategory))
                        <a href="{{ route('client.project-categories.index') }}"
                            class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                            Cancel
                        </a>
                    @endif
                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ isset($editCategory) ? 'Update Category' : 'Add Category' }}
                    </button>
                </div>
            </form>
        </div>

        {{-- 🔽 TABLE SECTION --}}
        <div class="bg-white rounded-xl shadow-sm">
            <!-- Header -->
            <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
                <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">All Categories</h3>
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                    <a href="{{ route('client.project-categories.index') }}"
                        class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                        Add New
                    </a>
                    <button
                        class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                        <i class="fas fa-download mr-2"></i> Export
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Category Name
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Slug
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-primary-900">{{ $category->name }}</td>
                                <td class="px-6 py-4 text-sm text-primary-600">{{ $category->slug }}</td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('client.project-categories.show', $category) }}"
                                            class="text-accent-600 hover:text-accent-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('client.project-categories.index', ['edit' => $category->id]) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST"
                                            action="{{ route('client.project-categories.destroy', $category) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-primary-600">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer / Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-primary-600">
                    Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of
                    {{ $categories->total() }} results
                </div>
                <x-pagination :paginator="$categories" />
            </div>
        </div>

    </div>
</x-client.layout.app>
